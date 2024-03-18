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
		// echo "<pre>";
		// var_dump($mastervehicle);die();
		// echo "<pre>";

		$getdatalastinfo = $this->m_poipoolmaster->getLastPosition("webtracking_gps", $mastervehicle[0]['vehicle_dbname_live'], $device0);
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
				$devicestatusfixnya = "";
				if ($loginbarudecode->result == 0) {
					$devicestatusfixnya = "";
				}else {
					if ($loginbarudecode->message == "Device does not exist!") {
						$devicestatusfixnya = "";
					}else {
						$statusfixnya       = $loginbarudecode->onlines[0]->online;
						$devicestatusfixnya = $statusfixnya;
					}
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
					 "vehicle_mv03" 					=> $mastervehicle[0]['vehicle_mv03'],
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
		// $user_dblive     = $this->sess->user_dblive;
		$key             = $_POST['key'];
		// $key             = "b 9442 wcb";
		// $keyfix          = str_replace(" ", "", $key);
		$keyfix          = $key;

		// echo "<pre>";
		// var_dump($keyfix);die();
		// echo "<pre>";

		$mastervehicle   = $this->m_poipoolmaster->searchmasterdata("webtracking_vehicle", $keyfix);

		if (sizeof($mastervehicle) < 1) {
			echo json_encode(array("code" => "400"));
		}else {
			// echo "<pre>";
			// var_dump($user_dblive);die();
			// echo "<pre>";
			$dblive = $mastervehicle[0]['vehicle_dbname_live'];

			$device          = explode("@", $mastervehicle[0]['vehicle_device']);
			$device0         = $device[0];
			$device1         = $device[1];
			$getdatalastinfo = $this->m_poipoolmaster->searchdblivedata("webtracking_gps", $dblive, $device0);
			$lastinfofix     = $this->gpsmodel->GetLastInfo($device0, $device1, true, false, 0, "");

			// echo "<pre>";
			// var_dump($mastervehicle[0]['vehicle_autocheck']);die();
			// echo "<pre>";

			$vehiclemv03 = $mastervehicle[0]['vehicle_mv03'];
			// if ($vehiclemv03 != "0000") {
			// 	// LOGIN API
			// 	$username        = "temanindobara";
			// 	$password        = "000000";
			// 	$loginbaru       = file_get_contents("http://gpsdvr.pilartech.co.id/StandardApiAction_login.action?account=".$username."&password=".$password);
			// 	$loginbarudecode = json_decode($loginbaru);
			// 	$jsession        = $loginbarudecode->jsession;
			//
			// 	$dataonline = file_get_contents("http://gpsdvr.pilartech.co.id/StandardApiAction_getDeviceStatus.action?jsession=".$jsession."&devIdno=".$vehiclemv03."&toMap=1&driver=0&language=en");
			// 		if ($dataonline) {
			// 			$datadecode = json_decode($dataonline);
			// 			$onlinestatus = $datadecode->status[0]->ol;
			// 				if ($onlinestatus == 1) {
			// 					$onlinestatus = "online";
			// 				}else {
			// 					$onlinestatus = "offline";
			// 				}
			// 				$devicestatusfixnya = $onlinestatus;
			// 		}else {
			// 			$devicestatusfixnya = "";
			// 		}
			// 		// echo "<pre>";
			// 		// var_dump($row->devicestatus);die();
			// 		// echo "<pre>";
			// }else {
				$devicestatusfixnya = "";
			// }

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
				$jsonnya[0]          = json_decode($getdatalastinfo[0]['vehicle_autocheck']);
				$json_master_vehicle = json_decode($mastervehicle[0]['vehicle_autocheck']);

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

					// echo "<pre>";
					// var_dump($jsonnya[0]);die();
					// echo "<pre>";

					array_push($datafix, array(
						 "drivername" 	           => $drivername,
						 "driverimage"             => $driverimage,
						 "vehicle_id"              => $mastervehicle[0]['vehicle_id'],
						 "vehicle_user_id"         => $mastervehicle[0]['vehicle_user_id'],
						 "vehicle_device"          => $mastervehicle[0]['vehicle_device'],
						 "vehicle_no"              => $mastervehicle[0]['vehicle_no'],
						 "vehicle_name"            => $mastervehicle[0]['vehicle_name'],
						 "vehicle_active_date2"    => $mastervehicle[0]['vehicle_active_date2'],
						 "vehicle_card_no"         => $mastervehicle[0]['vehicle_card_no'],
						 "vehicle_operator"        => $mastervehicle[0]['vehicle_operator'],
						 "vehicle_active_date"     => $mastervehicle[0]['vehicle_active_date'],
						 "vehicle_active_date1"    => $mastervehicle[0]['vehicle_active_date1'],
						 "vehicle_status"          => $mastervehicle[0]['vehicle_status'],
						 "vehicle_image"           => $mastervehicle[0]['vehicle_image'],
						 "vehicle_created_date"    => $mastervehicle[0]['vehicle_created_date'],
						 "vehicle_type"            => $mastervehicle[0]['vehicle_type'],
						 "vehicle_autorefill"      => $mastervehicle[0]['vehicle_autorefill'],
						 "vehicle_maxspeed"        => $mastervehicle[0]['vehicle_maxspeed'],
						 "vehicle_maxparking"      => $mastervehicle[0]['vehicle_maxparking'],
						 "vehicle_company"         => $mastervehicle[0]['vehicle_company'],
						 "vehicle_subcompany"      => $mastervehicle[0]['vehicle_subcompany'],
						 "vehicle_group"           => $mastervehicle[0]['vehicle_group'],
						 "vehicle_subgroup"        => $mastervehicle[0]['vehicle_subgroup'],
						 "vehicle_odometer"        => $mastervehicle[0]['vehicle_odometer'],
						 "vehicle_payment_type"    => $mastervehicle[0]['vehicle_payment_type'],
						 "vehicle_payment_amount"  => $mastervehicle[0]['vehicle_payment_amount'],
						 "vehicle_fuel_capacity"   => $mastervehicle[0]['vehicle_fuel_capacity'],
						 "vehicle_fuel_volt" 		   => $mastervehicle[0]['vehicle_fuel_volt'],
						 // "vehicle_info"         => $result[$i]['vehicle_info'],
						 "vehicle_sales"           => $mastervehicle[0]['vehicle_sales'],
						 "vehicle_teknisi_id"      => $mastervehicle[0]['vehicle_teknisi_id'],
						 "vehicle_port_time"       => date("d-m-Y H:i:s", strtotime($mastervehicle[0]['vehicle_port_time'])),
						 "vehicle_port_name"       => $mastervehicle[0]['vehicle_port_name'],
						 "vehicle_rom_time"        => date("d-m-Y H:i:s", strtotime($mastervehicle[0]['vehicle_rom_time'])),
						 "vehicle_rom_name"        => $mastervehicle[0]['vehicle_rom_name'],
						 "vehicle_tanggal_pasang"  => $mastervehicle[0]['vehicle_tanggal_pasang'],
						 "vehicle_imei"            => str_replace(array("\n","\r","'","'\'","/", "-"), "", $mastervehicle[0]['vehicle_imei']),
						 "vehicle_dbhistory"       => $mastervehicle[0]['vehicle_dbhistory'],
						 "vehicle_dbhistory_name"  => $mastervehicle[0]['vehicle_dbhistory_name'],
						 "vehicle_dbname_live"     => $mastervehicle[0]['vehicle_dbname_live'],
						 "vehicle_isred"           => $mastervehicle[0]['vehicle_isred'],
						 "vehicle_modem"           => $mastervehicle[0]['vehicle_modem'],
						 "vehicle_card_no_status"  => $mastervehicle[0]['vehicle_card_no_status'],
						 "devicestatusfixnya" 	   => $devicestatusfixnya,
						 "auto_last_road" 				 => $autolastroad,
						 "autolastritase" 				 => $autolastritase,
						 "auto_last_driver_name"   => str_replace(array("\n","\r","'","'\'","/", "-"), "", $json_master_vehicle->auto_last_driver_name),
						 "auto_last_driver_id"     => str_replace(array("\n","\r","'","'\'","/", "-"), "", $json_master_vehicle->auto_last_driver_id),
						 "auto_last_driver_time"   => date("d-m-Y H:i:s", strtotime($json_master_vehicle->auto_last_driver_time)),
						 "auto_status"             => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_status),
						 "auto_last_mvd"           => round($lastinfofix->gps_mvd),
						 "auto_last_update"        => $lastinfofix->gps_date_fmt. " ". $lastinfofix->gps_time_fmt,
						 "auto_last_check"         => $jsonnya[0]->auto_last_check,
						 "auto_last_snap"          => $snap,
						 "auto_last_snap_time"     => $snaptime,
						 "auto_last_position"      => str_replace(array("\n","\r","'","'\'","/", "-"), "", $lastinfofix->georeverse->display_name),
						 "auto_last_lat"           => substr($lastinfofix->gps_latitude_real_fmt, 0, 10),
						 "auto_last_long"          => substr($lastinfofix->gps_longitude_real_fmt, 0, 10),
						 "auto_last_engine"        => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_engine),
						 "auto_last_gpsstatus"     => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_gpsstatus),
						 "auto_last_speed"         => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_speed),
						 "auto_last_course"        => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_course),
						 "auto_flag"               => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_flag)
					));
			}else {
				$jsonnya[0] = json_decode($mastervehicle[0]['vehicle_autocheck']);
				$json_master_vehicle = json_decode($mastervehicle[0]['vehicle_autocheck']);


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
						 "drivername" 	             => $drivername,
						 "driverimage"               => $driverimage,
						 "vehicle_id"                => $mastervehicle[0]['vehicle_id'],
						 "vehicle_user_id"           => $mastervehicle[0]['vehicle_user_id'],
						 "vehicle_device"            => $mastervehicle[0]['vehicle_device'],
						 "vehicle_no"                => $mastervehicle[0]['vehicle_no'],
						 "vehicle_name"              => $mastervehicle[0]['vehicle_name'],
						 "vehicle_active_date2"      => $mastervehicle[0]['vehicle_active_date2'],
						 "vehicle_card_no"           => $mastervehicle[0]['vehicle_card_no'],
						 "vehicle_operator"          => $mastervehicle[0]['vehicle_operator'],
						 "vehicle_active_date"       => $mastervehicle[0]['vehicle_active_date'],
						 "vehicle_active_date1"      => $mastervehicle[0]['vehicle_active_date1'],
						 "vehicle_status"            => $mastervehicle[0]['vehicle_status'],
						 "vehicle_image"             => $mastervehicle[0]['vehicle_image'],
						 "vehicle_created_date"      => $mastervehicle[0]['vehicle_created_date'],
						 "vehicle_type"              => $mastervehicle[0]['vehicle_type'],
						 "vehicle_autorefill"        => $mastervehicle[0]['vehicle_autorefill'],
						 "vehicle_maxspeed"          => $mastervehicle[0]['vehicle_maxspeed'],
						 "vehicle_maxparking"        => $mastervehicle[0]['vehicle_maxparking'],
						 "vehicle_company"           => $mastervehicle[0]['vehicle_company'],
						 "vehicle_subcompany"        => $mastervehicle[0]['vehicle_subcompany'],
						 "vehicle_group"             => $mastervehicle[0]['vehicle_group'],
						 "vehicle_subgroup"          => $mastervehicle[0]['vehicle_subgroup'],
						 "vehicle_odometer"          => $mastervehicle[0]['vehicle_odometer'],
						 "vehicle_payment_type"      => $mastervehicle[0]['vehicle_payment_type'],
						 "vehicle_payment_amount"    => $mastervehicle[0]['vehicle_payment_amount'],
						 "vehicle_fuel_capacity"     => $mastervehicle[0]['vehicle_fuel_capacity'],
						 "vehicle_fuel_volt" 		     => $mastervehicle[0]['vehicle_fuel_volt'],
						 // "vehicle_info"           => $result[$i]['vehicle_info'],
						 "vehicle_sales"             => $mastervehicle[0]['vehicle_sales'],
						 "vehicle_teknisi_id"        => $mastervehicle[0]['vehicle_teknisi_id'],
						 "vehicle_port_time"         => date("d-m-Y H:i:s", strtotime($mastervehicle[0]['vehicle_port_time'])),
						 "vehicle_port_name"         => $mastervehicle[0]['vehicle_port_name'],
						 "vehicle_rom_time"          => date("d-m-Y H:i:s", strtotime($mastervehicle[0]['vehicle_rom_time'])),
						 "vehicle_rom_name"          => $mastervehicle[0]['vehicle_rom_name'],
						 "vehicle_tanggal_pasang"    => $mastervehicle[0]['vehicle_tanggal_pasang'],
						 "vehicle_imei"              => str_replace(array("\n","\r","'","'\'","/", "-"), "", $mastervehicle[0]['vehicle_imei']),
						 "vehicle_dbhistory"         => $mastervehicle[0]['vehicle_dbhistory'],
						 "vehicle_dbhistory_name"    => $mastervehicle[0]['vehicle_dbhistory_name'],
						 "vehicle_dbname_live"       => $mastervehicle[0]['vehicle_dbname_live'],
						 "vehicle_isred"             => $mastervehicle[0]['vehicle_isred'],
						 "vehicle_modem"             => $mastervehicle[0]['vehicle_modem'],
						 "vehicle_card_no_status"    => $mastervehicle[0]['vehicle_card_no_status'],
						 "devicestatusfixnya" 	     => $devicestatusfixnya,
						 "auto_last_road" 					 => $autolastroad,
						 "autolastritase" 				   => $autolastritase,
						 "auto_last_driver_name"     => str_replace(array("\n","\r","'","'\'","/", "-"), "", $json_master_vehicle->auto_last_driver_name),
						 "auto_last_driver_id"       => str_replace(array("\n","\r","'","'\'","/", "-"), "", $json_master_vehicle->auto_last_driver_id),
						 "auto_last_driver_time"   => date("d-m-Y H:i:s", strtotime($json_master_vehicle->auto_last_driver_time)),
						 "auto_status"               => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_status),
						 "auto_last_mvd"             => round($lastinfofix->gps_mvd),
						 "auto_last_update"          => $jsonnya[0]->auto_last_update,
						 "auto_last_check"           => $jsonnya[0]->auto_last_check,
						 "auto_last_snap"            => $snap,
						 "auto_last_snap_time"       => $snaptime,
						 "auto_last_position"        => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_position),
						 "auto_last_lat"             => substr($jsonnya[0]->auto_last_lat, 0, 10),
						 "auto_last_long"            => substr($jsonnya[0]->auto_last_long, 0, 10),
						 "auto_last_engine"          => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_engine),
						 "auto_last_gpsstatus"       => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_gpsstatus),
						 "auto_last_speed"           => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_speed),
						 "auto_last_course"          => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_last_course),
						 "auto_flag"                 => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[0]->auto_flag)
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

		$this->heatmap();

		// if($this->sess->user_id == "1445"){
		// 	$user_id = $this->sess->user_id; //tag
		// }else{
		// 	$user_id = $this->sess->user_id;
		// }
		//
		// $privilegecode = $this->sess->user_id_role;
		// $user_company  = $this->sess->user_company;
		// $user_parent   = $this->sess->user_parent;
		//
		// $user_id_fix 										= $user_id;
		//
		// $companyid                      = $this->sess->user_company;
		// $user_dblive                    = $this->sess->user_dblive;
		// $datafromdblive                 = $this->m_poipoolmaster->getfromdblive("webtracking_gps", $user_dblive);
		// $mastervehicle                  = $this->m_poipoolmaster->getmastervehicle();
		// //
		// $datafix            = array();
		// $datafixbgt         = array();
		// $deviceidygtidakada = array();
		//
		// for ($i=0; $i < sizeof($mastervehicle); $i++) {
		// 	// $device = $datafromdblive[$i]['gps_name'].'@'.$datafromdblive[$i]['gps_host'];
		// 	$device = explode("@", $mastervehicle[$i]['vehicle_device']);
		// 	$device0 = $device[0];
		// 	$device1 = $device[1];
		//
		// 	// print_r("devicenya : ".$device0);
		// 	// $getdata[] = $this->m_poipoolmaster->getmastervehiclebydevid($device);
		// 	$getdata[]                 = $this->m_poipoolmaster->getLastPosition("webtracking_gps", $user_dblive, $device0);
		// 	// $laspositionfromgpsmodel[] = $this->gpsmodel->GetLastInfo($device0, $device1, true, false, 0, "");
		// 		if (sizeof($getdata[$i]) > 0) {
		// 			// $jsonnya[] = json_decode($getdata[$i][0]['vehicle_autocheck'], true);
		// 					array_push($datafix, array(
  	// 					 "is_update" 						  => "yes",
		// 					 "vehicle_id"             => $mastervehicle[$i]['vehicle_id'],
		//  					 "vehicle_user_id"        => $mastervehicle[$i]['vehicle_user_id'],
		//  					 "vehicle_device"         => $mastervehicle[$i]['vehicle_device'],
		//  					 "vehicle_no"             => $mastervehicle[$i]['vehicle_no'],
		//  					 "vehicle_name"           => $mastervehicle[$i]['vehicle_name'],
		//  					 "vehicle_active_date2"   => $mastervehicle[$i]['vehicle_active_date2'],
		//  					 "vehicle_card_no"        => $mastervehicle[$i]['vehicle_card_no'],
		//  					 "vehicle_operator"       => $mastervehicle[$i]['vehicle_operator'],
		//  					 "vehicle_active_date"    => $mastervehicle[$i]['vehicle_active_date'],
		//  					 "vehicle_active_date1"   => $mastervehicle[$i]['vehicle_active_date1'],
		//  					 "vehicle_status"         => $mastervehicle[$i]['vehicle_status'],
		//  					 "vehicle_image"          => $mastervehicle[$i]['vehicle_image'],
		//  					 "vehicle_created_date"   => $mastervehicle[$i]['vehicle_created_date'],
		//  					 "vehicle_type"           => $mastervehicle[$i]['vehicle_type'],
		//  					 "vehicle_autorefill"     => $mastervehicle[$i]['vehicle_autorefill'],
		//  					 "vehicle_maxspeed"       => $mastervehicle[$i]['vehicle_maxspeed'],
		//  					 "vehicle_maxparking"     => $mastervehicle[$i]['vehicle_maxparking'],
		//  					 "vehicle_company"        => $mastervehicle[$i]['vehicle_company'],
		//  					 "vehicle_subcompany"     => $mastervehicle[$i]['vehicle_subcompany'],
		//  					 "vehicle_group"          => $mastervehicle[$i]['vehicle_group'],
		//  					 "vehicle_subgroup"       => $mastervehicle[$i]['vehicle_subgroup'],
		//  					 "vehicle_odometer"       => $mastervehicle[$i]['vehicle_odometer'],
		//  					 "vehicle_payment_type"   => $mastervehicle[$i]['vehicle_payment_type'],
		//  					 "vehicle_payment_amount" => $mastervehicle[$i]['vehicle_payment_amount'],
		//  					 "vehicle_fuel_capacity"  => $mastervehicle[$i]['vehicle_fuel_capacity'],
		// 					 "vehicle_fuel_volt" 		  => $mastervehicle[$i]['vehicle_fuel_volt'],
		//  					 // "vehicle_info"           => $result[$i]['vehicle_info'],
		//  					 "vehicle_sales"          => $mastervehicle[$i]['vehicle_sales'],
		//  					 "vehicle_teknisi_id"     => $mastervehicle[$i]['vehicle_teknisi_id'],
		//  					 "vehicle_tanggal_pasang" => $mastervehicle[$i]['vehicle_tanggal_pasang'],
		//  					 "vehicle_imei"           => str_replace(array("\n","\r","'","'\'","/", "-"), "", $mastervehicle[$i]['vehicle_imei']),
		//  					 "vehicle_dbhistory"      => $mastervehicle[$i]['vehicle_dbhistory'],
		//  					 "vehicle_dbhistory_name" => $mastervehicle[$i]['vehicle_dbhistory_name'],
		//  					 "vehicle_dbname_live"    => $mastervehicle[$i]['vehicle_dbname_live'],
		//  					 "vehicle_isred"          => $mastervehicle[$i]['vehicle_isred'],
		//  					 "vehicle_modem"          => $mastervehicle[$i]['vehicle_modem'],
		//  					 "vehicle_card_no_status" => $mastervehicle[$i]['vehicle_card_no_status'],
		// 					 "vehicle_mv03" 					=> $mastervehicle[$i]['vehicle_mv03'],
		// 					 // "position"  	  				  => $laspositionfromgpsmodel[$i]->georeverse->display_name,
		// 					 "vehicle_autocheck" 	 		=> $getdata[$i][0]['vehicle_autocheck']
		// 					));
		// 		}else {
		// 			// $jsonnya2[$i] = json_decode($mastervehicle[$i]['vehicle_autocheck'], true);
		// 			array_push($deviceidygtidakada, array(
		// 				"is_update" 						 => "no",
		// 				"vehicle_id"             => $mastervehicle[$i]['vehicle_id'],
		// 				"vehicle_user_id"        => $mastervehicle[$i]['vehicle_user_id'],
		// 				"vehicle_device"         => $mastervehicle[$i]['vehicle_device'],
		// 				"vehicle_no"             => $mastervehicle[$i]['vehicle_no'],
		// 				"vehicle_name"           => $mastervehicle[$i]['vehicle_name'],
		// 				"vehicle_active_date2"   => $mastervehicle[$i]['vehicle_active_date2'],
		// 				"vehicle_card_no"        => $mastervehicle[$i]['vehicle_card_no'],
		// 				"vehicle_operator"       => $mastervehicle[$i]['vehicle_operator'],
		// 				"vehicle_active_date"    => $mastervehicle[$i]['vehicle_active_date'],
		// 				"vehicle_active_date1"   => $mastervehicle[$i]['vehicle_active_date1'],
		// 				"vehicle_status"         => $mastervehicle[$i]['vehicle_status'],
		// 				"vehicle_image"          => $mastervehicle[$i]['vehicle_image'],
		// 				"vehicle_created_date"   => $mastervehicle[$i]['vehicle_created_date'],
		// 				"vehicle_type"           => $mastervehicle[$i]['vehicle_type'],
		// 				"vehicle_autorefill"     => $mastervehicle[$i]['vehicle_autorefill'],
		// 				"vehicle_maxspeed"       => $mastervehicle[$i]['vehicle_maxspeed'],
		// 				"vehicle_maxparking"     => $mastervehicle[$i]['vehicle_maxparking'],
		// 				"vehicle_company"        => $mastervehicle[$i]['vehicle_company'],
		// 				"vehicle_subcompany"     => $mastervehicle[$i]['vehicle_subcompany'],
		// 				"vehicle_group"          => $mastervehicle[$i]['vehicle_group'],
		// 				"vehicle_subgroup"       => $mastervehicle[$i]['vehicle_subgroup'],
		// 				"vehicle_odometer"       => $mastervehicle[$i]['vehicle_odometer'],
		// 				"vehicle_payment_type"   => $mastervehicle[$i]['vehicle_payment_type'],
		// 				"vehicle_payment_amount" => $mastervehicle[$i]['vehicle_payment_amount'],
		// 				"vehicle_fuel_capacity"  => $mastervehicle[$i]['vehicle_fuel_capacity'],
		// 				"vehicle_fuel_volt" 		  => $mastervehicle[$i]['vehicle_fuel_volt'],
		// 				// "vehicle_info"           => $result[$i]['vehicle_info'],
		// 				"vehicle_sales"          => $mastervehicle[$i]['vehicle_sales'],
		// 				"vehicle_teknisi_id"     => $mastervehicle[$i]['vehicle_teknisi_id'],
		// 				"vehicle_tanggal_pasang" => $mastervehicle[$i]['vehicle_tanggal_pasang'],
		// 				"vehicle_imei"           => str_replace(array("\n","\r","'","'\'","/", "-"), "", $mastervehicle[$i]['vehicle_imei']),
		// 				"vehicle_dbhistory"      => $mastervehicle[$i]['vehicle_dbhistory'],
		// 				"vehicle_dbhistory_name" => $mastervehicle[$i]['vehicle_dbhistory_name'],
		// 				"vehicle_dbname_live"    => $mastervehicle[$i]['vehicle_dbname_live'],
		// 				"vehicle_isred"          => $mastervehicle[$i]['vehicle_isred'],
		// 				"vehicle_modem"          => $mastervehicle[$i]['vehicle_modem'],
		// 				"vehicle_card_no_status" => $mastervehicle[$i]['vehicle_card_no_status'],
		// 				"vehicle_mv03" 					 => $mastervehicle[$i]['vehicle_mv03'],
		// 				// "position"  	  				 => $laspositionfromgpsmodel[$i]->georeverse->display_name,
		// 				"vehicle_autocheck" 	 	 => $mastervehicle[$i]['vehicle_autocheck']
		// 			));
		// 		}
		// }
		//
		// // echo "<pre>";
		// // var_dump($datafix[2]['vehicle_autocheck']);die();
		// // echo "<pre>";
		//
		// $datafixbgt = array_merge($datafix, $deviceidygtidakada);
		// $throwdatatoview = array();
		// for ($loop=0; $loop < sizeof($datafixbgt); $loop++) {
		// 	$jsonnya[$loop] = json_decode($datafixbgt[$loop]['vehicle_autocheck'], true);
		// 		if (isset($jsonnya[$loop]['auto_last_road'])) {
		// 			$autolastroad = str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$loop]['auto_last_road']);
		// 		}else {
		// 			$autolastroad = str_replace(array("\n","\r","'","'\'","/", "-"), "", "");
		// 		}
		//
		// 		if (isset($jsonnya[$loop]['auto_last_ritase'])) {
		// 			$autolastritase = str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$loop]['auto_last_ritase']);
		// 		}else {
		// 			$autolastritase = str_replace(array("\n","\r","'","'\'","/", "-"), "", "");
		// 		}
		//
		// 		if (isset($jsonnya[$loop]['auto_last_mvd'])) {
		// 			$autolastfuel = $jsonnya[$loop]['auto_last_mvd'];
		// 		}else {
		// 			$autolastfuel = "";
		// 		}
		//
		// 	array_push($throwdatatoview, array(
		// 		"is_update" 						 => $datafixbgt[$loop]['is_update'],
		// 		"vehicle_id"             => $datafixbgt[$loop]['vehicle_id'],
		// 		"vehicle_user_id"        => $datafixbgt[$loop]['vehicle_user_id'],
		// 		"vehicle_device"         => $datafixbgt[$loop]['vehicle_device'],
		// 		"vehicle_no"             => $datafixbgt[$loop]['vehicle_no'],
		// 		"vehicle_name"           => $datafixbgt[$loop]['vehicle_name'],
		// 		"vehicle_active_date2"   => $datafixbgt[$loop]['vehicle_active_date2'],
		// 		"vehicle_card_no"        => $datafixbgt[$loop]['vehicle_card_no'],
		// 		"vehicle_operator"       => $datafixbgt[$loop]['vehicle_operator'],
		// 		"vehicle_active_date"    => $datafixbgt[$loop]['vehicle_active_date'],
		// 		"vehicle_active_date1"   => $datafixbgt[$loop]['vehicle_active_date1'],
		// 		"vehicle_status"         => $datafixbgt[$loop]['vehicle_status'],
		// 		"vehicle_image"          => $datafixbgt[$loop]['vehicle_image'],
		// 		"vehicle_created_date"   => $datafixbgt[$loop]['vehicle_created_date'],
		// 		"vehicle_type"           => $datafixbgt[$loop]['vehicle_type'],
		// 		"vehicle_autorefill"     => $datafixbgt[$loop]['vehicle_autorefill'],
		// 		"vehicle_maxspeed"       => $datafixbgt[$loop]['vehicle_maxspeed'],
		// 		"vehicle_maxparking"     => $datafixbgt[$loop]['vehicle_maxparking'],
		// 		"vehicle_company"        => $datafixbgt[$loop]['vehicle_company'],
		// 		"vehicle_subcompany"     => $datafixbgt[$loop]['vehicle_subcompany'],
		// 		"vehicle_group"          => $datafixbgt[$loop]['vehicle_group'],
		// 		"vehicle_subgroup"       => $datafixbgt[$loop]['vehicle_subgroup'],
		// 		"vehicle_odometer"       => $datafixbgt[$loop]['vehicle_odometer'],
		// 		"vehicle_payment_type"   => $datafixbgt[$loop]['vehicle_payment_type'],
		// 		"vehicle_payment_amount" => $datafixbgt[$loop]['vehicle_payment_amount'],
		// 		"vehicle_fuel_capacity"  => $datafixbgt[$loop]['vehicle_fuel_capacity'],
		// 		"vehicle_fuel_volt" 		 => $datafixbgt[$loop]['vehicle_fuel_volt'],
		// 		"vehicle_sales"          => $datafixbgt[$loop]['vehicle_sales'],
		// 		"vehicle_teknisi_id"     => $datafixbgt[$loop]['vehicle_teknisi_id'],
		// 		"vehicle_tanggal_pasang" => $datafixbgt[$loop]['vehicle_tanggal_pasang'],
		// 		"vehicle_imei"           => str_replace(array("\n","\r","'","'\'","/", "-"), "", $datafixbgt[$loop]['vehicle_imei']),
		// 		"vehicle_dbhistory"      => $datafixbgt[$loop]['vehicle_dbhistory'],
		// 		"vehicle_dbhistory_name" => $datafixbgt[$loop]['vehicle_dbhistory_name'],
		// 		"vehicle_dbname_live"    => $datafixbgt[$loop]['vehicle_dbname_live'],
		// 		"vehicle_isred"          => $datafixbgt[$loop]['vehicle_isred'],
		// 		"vehicle_modem"          => $datafixbgt[$loop]['vehicle_modem'],
		// 		"vehicle_card_no_status" => $datafixbgt[$loop]['vehicle_card_no_status'],
		// 		"vehicle_mv03" 					 => $datafixbgt[$loop]['vehicle_mv03'],
		// 		"auto_last_road"         => $autolastroad,
		// 		"auto_last_ritase"       => $autolastritase,
		// 		"auto_last_fuel"         => $autolastfuel,
		// 		"auto_status"            => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$loop]['auto_status']),
		// 		"auto_last_update"       => date("d F Y H:i:s", strtotime($jsonnya[$loop]['auto_last_update'])),
		// 		"auto_last_check"        => $jsonnya[$loop]['auto_last_check'],
		// 		// "auto_last_position"     => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$loop]['auto_last_position']),
		// 		"auto_last_lat"          => substr($jsonnya[$loop]['auto_last_lat'], 0, 10),
		// 		"auto_last_long"         => substr($jsonnya[$loop]['auto_last_long'], 0, 10),
		// 		"auto_last_engine"       => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$loop]['auto_last_engine']),
		// 		"auto_last_gpsstatus"    => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$loop]['auto_last_gpsstatus']),
		// 		"auto_last_speed"        => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$loop]['auto_last_speed']),
		// 		"auto_last_course"       => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$loop]['auto_last_course']),
		// 		"auto_flag"              => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$loop]['auto_flag'])
		// 	));
		// }
		//
		// $company                  = $this->dashboardmodel->getcompany_byowner($privilegecode);
		// $datavehicleandcompany    = array();
		// $datavehicleandcompanyfix = array();
		//
		// 	for ($d=0; $d < sizeof($company); $d++) {
		// 		$vehicledata[$d]   = $this->dashboardmodel->getvehicle_bycompany_master($company[$d]->company_id);
		// 		// $vehiclestatus[$d] = $this->dashboardmodel->getjson_status2($vehicledata[1][0]->vehicle_device);
		// 		$totaldata         = $this->dashboardmodel->gettotalengine($company[$d]->company_id);
		// 		$totalengine       = explode("|", $totaldata);
		// 			array_push($datavehicleandcompany, array(
		// 				"company_id"   => $company[$d]->company_id,
		// 				"company_name" => $company[$d]->company_name,
		// 				"totalmobil"   => $totalengine[2],
		// 				"vehicle"      => $vehicledata[$d]
		// 			));
		// 	}
		//
		// 	// echo "<pre>";
		// 	// var_dump($jsonnya);die();
		// 	// echo "<pre>";
		//
		// $this->params['company']        = $company;
		// $this->params['companyid']      = $companyid;
		// $this->params['url_code_view']  = "1";
		// $this->params['code_view_menu'] = "monitor";
		// $this->params['maps_code']      = "morehundred";
		//
		// $rstatus                        = $this->dashboardmodel->gettotalstatus($this->sess->user_id);
		// $datastatus                     = explode("|", $rstatus);
		// $this->params['total_online']   = $datastatus[0]+$datastatus[1]; //p + K
		// $this->params['total_vehicle']  = $datastatus[3];
		// $this->params['total_offline']  = $datastatus[2];
		//
		// $this->params['vehicle']      = $datavehicleandcompany;
		// $this->params['vehicledata']  = $throwdatatoview;
		// $this->params['vehicletotal'] = sizeof($mastervehicle);
		// $this->params['poolmaster']   = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster", $user_id_fix);
		// $getvehicle_byowner           = $this->dashboardmodel->getvehicle_byowner();
		// $totalmobilnya                = sizeof($getvehicle_byowner);
		// if ($totalmobilnya == 0) {
	  //   $this->params['name']         = "0";
	  //   $this->params['host']         = "0";
	  // }else {
	  //   $arr          = explode("@", $getvehicle_byowner[0]->vehicle_device);
	  //   $this->params['name']         = $arr[0];
	  //   $this->params['host']         = $arr[1];
	  // }
		//
		// $this->params['resultactive']   = $this->dashboardmodel->vehicleactive();
	  // $this->params['resultexpired']  = $this->dashboardmodel->vehicleexpired();
	  // $this->params['resulttotaldev'] = $this->dashboardmodel->totaldevice();
		//
		// // echo "<pre>";
		// // var_dump($this->params['vehicledata']);die();
		// // echo "<pre>";
		//
		// $this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		// $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
		// $this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
		// $this->params["content"]        = $this->load->view('newdashboard/trackers/maps_view_kalimantan', $this->params, true);
		// $this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
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

	$user_id       = $this->sess->user_id;
	$user_parent   = $this->sess->user_parent;
	$privilegecode = $this->sess->user_id_role;
	$user_company  = $this->sess->user_company;

	if($privilegecode == 0){
		$user_id_fix = $user_id;
	}elseif ($privilegecode == 1) {
		$user_id_fix = $user_parent;
	}elseif ($privilegecode == 2) {
		$user_id_fix = $user_parent;
	}elseif ($privilegecode == 3) {
		$user_id_fix = $user_parent;
	}elseif ($privilegecode == 4) {
		$user_id_fix = $user_parent;
	}elseif ($privilegecode == 5) {
		$user_id_fix = $user_id;
	}elseif ($privilegecode == 6) {
		$user_id_fix = $user_id;
	}else{
		$user_id_fix = $user_id;
	}

	$companyid                       = $this->sess->user_company;
	$user_dblive                     = $this->sess->user_dblive;
	$mastervehicle                   = $this->m_poipoolmaster->getmastervehicleforheatmap();

	$datafix                         = array();
	$deviceidygtidakada              = array();
	$statusvehicle['engine_on']  = 0;
	$statusvehicle['engine_off'] = 0;

	for ($i=0; $i < sizeof($mastervehicle); $i++) {
		$jsonautocheck = json_decode($mastervehicle[$i]['vehicle_autocheck']);
		if (isset($jsonautocheck->auto_status)) {
			// code...
		$auto_status   = $jsonautocheck->auto_status;

		if ($privilegecode == 5 || $privilegecode == 6) {
			if ($mastervehicle[$i]['vehicle_company'] == $user_company) {
				if ($jsonautocheck->auto_last_engine == "ON") {
					$statusvehicle['engine_on'] += 1;
				}else {
					$statusvehicle['engine_off'] += 1;
				}
			}
		}else {
			if ($jsonautocheck->auto_last_engine == "ON") {
				$statusvehicle['engine_on'] += 1;
			}else {
				$statusvehicle['engine_off'] += 1;
			}
		}



			if ($auto_status != "M") {
				array_push($datafix, array(
					"vehicle_id"           => $mastervehicle[$i]['vehicle_id'],
					"vehicle_user_id"      => $mastervehicle[$i]['vehicle_user_id'],
					"vehicle_company"      => $mastervehicle[$i]['vehicle_company'],
					"vehicle_device"       => $mastervehicle[$i]['vehicle_device'],
					"vehicle_no"           => $mastervehicle[$i]['vehicle_no'],
					"vehicle_name"         => $mastervehicle[$i]['vehicle_name'],
					"vehicle_active_date2" => $mastervehicle[$i]['vehicle_active_date2'],
					"vehicle_is_share"     => $mastervehicle[$i]['vehicle_is_share'],
					"vehicle_id_shareto"   => $mastervehicle[$i]['vehicle_id_shareto'],
					"auto_last_lat"        => substr($jsonautocheck->auto_last_lat, 0, 10),
					"auto_last_long"       => substr($jsonautocheck->auto_last_long, 0, 10),
				));
			}
		}
	}

	$company                  = $this->dashboardmodel->getcompany_byowner($privilegecode);
		if ($company) {

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
		}else {
			$this->params['company']   = 0;
			$this->params['companyid'] = 0;
			$this->params['vehicle']   = 0;
		}

	// echo "<pre>";
	// var_dump($company);die();
	// echo "<pre>";


	$this->params['url_code_view']  = "1";
	$this->params['code_view_menu'] = "monitor";
	$this->params['maps_code']      = "morehundred";

	$this->params['engine_on']      = $statusvehicle['engine_on'];
	$this->params['engine_off']     = $statusvehicle['engine_off'];


	$rstatus                        = $this->dashboardmodel->gettotalstatus($this->sess->user_id);

	$datastatus                     = explode("|", $rstatus);
	$this->params['total_online']   = $datastatus[0]+$datastatus[1]; //p + K
	$this->params['total_vehicle']  = $datastatus[3];
	$this->params['total_offline']  = $datastatus[2];

	$this->params['vehicles']  	  = $mastervehicle;
	$this->params['vehicledata']  = $datafix;
	$this->params['vehicletotal'] = sizeof($mastervehicle);
	$this->params['poolmaster']   = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");
	$getvehicle_byowner           = $this->dashboardmodel->getvehicle_byownerforheatmap();
	// echo "<pre>";
	// var_dump($getvehicle_byowner);die();
	// echo "<pre>";
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
	$this->params['mapsetting']     = $this->m_poipoolmaster->getmapsetting();
	$this->params['poolmaster']     = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");

	// echo "<pre>";
	// var_dump($this->params['mapsetting']);die();
	// echo "<pre>";

	$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
	$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/trackers/maps_view_heatmap2', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		}elseif ($privilegecode == 2) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/trackers/maps_view_heatmap2', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
		}elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/trackers/maps_view_heatmap2', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		}elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/trackers/maps_view_heatmap2', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		}elseif ($privilegecode == 5) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/trackers/maps_view_heatmap2', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
		}elseif ($privilegecode == 6) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/trackers/maps_view_heatmap2', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
		}else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/trackers/maps_view_heatmap2', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
}

function monitoring_dev(){
	ini_set('max_execution_time', '300');
	set_time_limit(300);
	if (! isset($this->sess->user_type))
	{
		redirect(base_url());
	}

	$user_id       = $this->sess->user_id;
	$user_parent   = $this->sess->user_parent;
	$privilegecode = $this->sess->user_id_role;
	$user_company  = $this->sess->user_company;

	if($privilegecode == 0){
		$user_id_fix = $user_id;
	}elseif ($privilegecode == 1) {
		$user_id_fix = $user_parent;
	}elseif ($privilegecode == 2) {
		$user_id_fix = $user_parent;
	}elseif ($privilegecode == 3) {
		$user_id_fix = $user_parent;
	}elseif ($privilegecode == 4) {
		$user_id_fix = $user_parent;
	}elseif ($privilegecode == 5) {
		$user_id_fix = $user_id;
	}elseif ($privilegecode == 6) {
		$user_id_fix = $user_id;
	}else{
		$user_id_fix = $user_id;
	}

	$companyid                       = $this->sess->user_company;
	$user_dblive                     = $this->sess->user_dblive;
	$mastervehicle                   = $this->m_poipoolmaster->getmastervehicleforheatmap();

	$datafix                         = array();
	$deviceidygtidakada              = array();
	$statusvehicle['engine_on']  = 0;
	$statusvehicle['engine_off'] = 0;

	for ($i=0; $i < sizeof($mastervehicle); $i++) {
		$jsonautocheck = json_decode($mastervehicle[$i]['vehicle_autocheck']);
		$auto_status   = $jsonautocheck->auto_status;

		if ($privilegecode == 5 || $privilegecode == 6) {
			if ($mastervehicle[$i]['vehicle_company'] == $user_company) {
				if ($jsonautocheck->auto_last_engine == "ON") {
					$statusvehicle['engine_on'] += 1;
				}else {
					$statusvehicle['engine_off'] += 1;
				}
			}
		}else {
			if ($jsonautocheck->auto_last_engine == "ON") {
				$statusvehicle['engine_on'] += 1;
			}else {
				$statusvehicle['engine_off'] += 1;
			}
		}

			if ($auto_status != "M") {
				array_push($datafix, array(
					"vehicle_id"             => $mastervehicle[$i]['vehicle_id'],
					"vehicle_user_id"        => $mastervehicle[$i]['vehicle_user_id'],
					"vehicle_company"        => $mastervehicle[$i]['vehicle_company'],
					"vehicle_device"         => $mastervehicle[$i]['vehicle_device'],
					"vehicle_no"             => $mastervehicle[$i]['vehicle_no'],
					"vehicle_name"           => $mastervehicle[$i]['vehicle_name'],
					"vehicle_active_date2"   => $mastervehicle[$i]['vehicle_active_date2'],
					"auto_last_lat"          => substr($jsonautocheck->auto_last_lat, 0, 10),
					"auto_last_long"         => substr($jsonautocheck->auto_last_long, 0, 10),
				));
			}
	}

	$company                  = $this->dashboardmodel->getcompany_byowner($privilegecode);
		if ($company) {

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
		}else {
			$this->params['company']   = 0;
			$this->params['companyid'] = 0;
			$this->params['vehicle']   = 0;
		}

	// echo "<pre>";
	// var_dump($company);die();
	// echo "<pre>";


	$this->params['url_code_view']  = "1";
	$this->params['code_view_menu'] = "monitor";
	$this->params['maps_code']      = "morehundred";

	$this->params['engine_on']      = $statusvehicle['engine_on'];
	$this->params['engine_off']     = $statusvehicle['engine_off'];


	$rstatus                        = $this->dashboardmodel->gettotalstatus($this->sess->user_id);

	$datastatus                     = explode("|", $rstatus);
	$this->params['total_online']   = $datastatus[0]+$datastatus[1]; //p + K
	$this->params['total_vehicle']  = $datastatus[3];
	$this->params['total_offline']  = $datastatus[2];

	$this->params['vehicledata']  = $datafix;
	$this->params['vehicletotal'] = sizeof($mastervehicle);
	$this->params['poolmaster']   = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");
	$getvehicle_byowner           = $this->dashboardmodel->getvehicle_byownerforheatmap();
	// echo "<pre>";
	// var_dump($getvehicle_byowner);die();
	// echo "<pre>";
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
	$this->params['mapsetting']     = $this->m_poipoolmaster->getmapsetting();
	$this->params['poolmaster']     = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");

	// echo "<pre>";
	// var_dump($this->params['mapsetting']);die();
	// echo "<pre>";

	$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
	$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/trackers/maps_view_monitoring', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		}elseif ($privilegecode == 2) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/trackers/maps_view_monitoring', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
		}elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/trackers/maps_view_monitoring', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		}elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/trackers/maps_view_monitoring', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		}elseif ($privilegecode == 5) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/trackers/maps_view_monitoring', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
		}elseif ($privilegecode == 6) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/trackers/maps_view_monitoring', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
		}else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/trackers/maps_view_monitoring', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
}

function getsummarymapsoptionawal(){
	$summary_mapsoption              = $_POST['summary_mapsoption'];
	$company_id 			               = $_POST['contractor'];
	$typeofstreetrom		             = 3;
	$typeofstreetport		             = 4;
	$getallcompany    							 = $this->m_poipoolmaster->getAllCompany();
	$masterdatavehicle               = $this->m_poipoolmaster->getmastervehiclebycontractor($company_id);
	$streetRom                       = $this->m_poipoolmaster->getstreet_now($typeofstreetrom);
	$portStreet                      = $this->m_poipoolmaster->getstreet_now2($typeofstreetport);
	$portCPBIB                       = $this->m_poipoolmaster->getstreet_now2(7);
	$portANTBIR                      = $this->m_poipoolmaster->getstreet_now2(8);
	$dataallunit 						         = array();
	$datainrom                       = 0;
	$datainport                      = 0;
	$dataoutofhauling                = 0;
	$dataofflinevehicle              = 0;
	$datainpool 										 = 0;
	$datakosongan                    = 0;
	$datamuatan                      = 0;

	// DATA IN ROM
	for ($j=0; $j < sizeof($streetRom); $j++) {
		$street_name                = explode(",", $streetRom[$j]['street_name']);
		$street_namefix             = $street_name[0];
		$dataState[$street_namefix] = 0;

		for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
			$autocheck          = json_decode($masterdatavehicle[$i]['vehicle_autocheck']);
			$auto_last_position = explode(",", $autocheck->auto_last_position);
			$datalastposition   = $auto_last_position[0];
			$auto_status 		    = $autocheck->auto_status;

			if ($company_id != 0) {
				if ($summary_mapsoption == 0) {
					if ($auto_status != "M") {
						if ($datalastposition == $street_namefix) {
								$datainrom += 1;
						}
					}
				}elseif ($summary_mapsoption == 1) {
						if ($jsonautocheck->auto_last_engine == "ON") {
							if ($auto_status != "M") {
								if ($datalastposition == $street_namefix) {
										$datainrom += 1;
								}
							}
						}
					}else {
						if ($jsonautocheck->auto_last_engine == "OFF") {
							if ($auto_status != "M") {
								if ($datalastposition == $street_namefix) {
										$datainrom += 1;
								}
							}
						}
					}
			}else {
				if ($summary_mapsoption == 0) {
					if ($auto_status != "M") {
						if ($datalastposition == $street_namefix) {
								$datainrom += 1;
						}
					}
				}elseif ($summary_mapsoption == 1) {
						if ($jsonautocheck->auto_last_engine == "ON") {
							if ($auto_status != "M") {
								if ($datalastposition == $street_namefix) {
										$datainrom += 1;
								}
							}
						}
					}else {
						if ($jsonautocheck->auto_last_engine == "OFF") {
							if ($auto_status != "M") {
								if ($datalastposition == $street_namefix) {
										$datainrom += 1;
								}
							}
						}
					}
			}
		}
	}

	// DATA IN PORT
	for ($k=0; $k < sizeof($portStreet); $k++) {
		$street_name_port                   = explode(",", $portStreet[$k]['street_name']);
		$street_nameportfix                 = $street_name_port[0];
		$dataStatePort[$street_nameportfix] = 0;

		for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
			$autocheck          = json_decode($masterdatavehicle[$i]['vehicle_autocheck']);
			$auto_last_position = explode(",", $autocheck->auto_last_position);
			$datalastposition   = $auto_last_position[0];
			$auto_status 		    = $autocheck->auto_status;

			if ($company_id != 0) {
				if ($summary_mapsoption == 0) {
					if ($auto_status != "M") {
						if ($datalastposition == $street_nameportfix) {
								$datainport += 1;
						}
					}
				}elseif ($summary_mapsoption == 1) {
						if ($jsonautocheck->auto_last_engine == "ON") {
							if ($auto_status != "M") {
								if ($datalastposition == $street_nameportfix) {
										$datainport += 1;
								}
							}
						}
					}else {
						if ($jsonautocheck->auto_last_engine == "OFF") {
							if ($auto_status != "M") {
								if ($datalastposition == $street_nameportfix) {
										$datainport += 1;
								}
							}
						}
					}
			}else {
				if ($summary_mapsoption == 0) {
					if ($auto_status != "M") {
						if ($datalastposition == $street_nameportfix) {
								$datainport += 1;
						}
					}
				}elseif ($summary_mapsoption == 1) {
						if ($jsonautocheck->auto_last_engine == "ON") {
							if ($auto_status != "M") {
								if ($datalastposition == $street_nameportfix) {
										$datainport += 1;
								}
							}
						}
					}else {
						if ($jsonautocheck->auto_last_engine == "OFF") {
							if ($auto_status != "M") {
								if ($datalastposition == $street_nameportfix) {
										$datainport += 1;
								}
							}
						}
					}
			}
		}
	}

	for ($l=0; $l < sizeof($portCPBIB); $l++) {
		$street_name_portCPBIB                   = explode(",", $portCPBIB[$l]['street_name']);
		$street_nameportCPBIBfix                 = $street_name_portCPBIB[0];
		$dataStatePortCPBIB[$street_nameportCPBIBfix] = 0;

		for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
			$autocheck          = json_decode($masterdatavehicle[$i]['vehicle_autocheck']);
			$auto_last_position = explode(",", $autocheck->auto_last_position);
			$datalastposition   = $auto_last_position[0];
			$auto_status 		    = $autocheck->auto_status;

			if ($company_id != 0) {
				if ($summary_mapsoption == 0) {
					if ($auto_status != "M") {
						if ($datalastposition == $street_nameportCPBIBfix) {
								$datainport += 1;
						}
					}
				}elseif ($summary_mapsoption == 1) {
						if ($jsonautocheck->auto_last_engine == "ON") {
							if ($auto_status != "M") {
								if ($datalastposition == $street_nameportCPBIBfix) {
										$datainport += 1;
								}
							}
						}
					}else {
						if ($jsonautocheck->auto_last_engine == "OFF") {
							if ($auto_status != "M") {
								if ($datalastposition == $street_nameportCPBIBfix) {
										$datainport += 1;
								}
							}
						}
					}
			}else {
				if ($summary_mapsoption == 0) {
					if ($auto_status != "M") {
						if ($datalastposition == $street_nameportCPBIBfix) {
								$datainport += 1;
						}
					}
				}elseif ($summary_mapsoption == 1) {
						if ($jsonautocheck->auto_last_engine == "ON") {
							if ($auto_status != "M") {
								if ($datalastposition == $street_nameportCPBIBfix) {
										$datainport += 1;
								}
							}
						}
					}else {
						if ($jsonautocheck->auto_last_engine == "OFF") {
							if ($auto_status != "M") {
								if ($datalastposition == $street_nameportCPBIBfix) {
										$datainport += 1;
								}
							}
						}
					}
			}
		}
	}

	for ($m=0; $m < sizeof($portANTBIR); $m++) {
		$street_name_portANTBIR                   = explode(",", $portANTBIR[$m]['street_name']);
		$street_nameportANTBIRfix                 = $street_name_portANTBIR[0];
		$dataStatePortANTBIR[$street_nameportANTBIRfix] = 0;

		for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
			$autocheck          = json_decode($masterdatavehicle[$i]['vehicle_autocheck']);
			$auto_last_position = explode(",", $autocheck->auto_last_position);
			$datalastposition   = $auto_last_position[0];
			$auto_status 		    = $autocheck->auto_status;

			if ($company_id != 0) {
				if ($summary_mapsoption == 0) {
					if ($auto_status != "M") {
						if ($datalastposition == $street_nameportANTBIRfix) {
								$datainport += 1;
						}
					}
				}elseif ($summary_mapsoption == 1) {
						if ($jsonautocheck->auto_last_engine == "ON") {
							if ($auto_status != "M") {
								if ($datalastposition == $street_nameportANTBIRfix) {
										$datainport += 1;
								}
							}
						}
					}else {
						if ($jsonautocheck->auto_last_engine == "OFF") {
							if ($auto_status != "M") {
								if ($datalastposition == $street_nameportANTBIRfix) {
										$datainport += 1;
								}
							}
						}
					}
			}else {
				if ($summary_mapsoption == 0) {
					if ($auto_status != "M") {
						if ($datalastposition == $street_nameportANTBIRfix) {
								$datainport += 1;
						}
					}
				}elseif ($summary_mapsoption == 1) {
						if ($jsonautocheck->auto_last_engine == "ON") {
							if ($auto_status != "M") {
								if ($datalastposition == $street_nameportANTBIRfix) {
										$datainport += 1;
								}
							}
						}
					}else {
						if ($jsonautocheck->auto_last_engine == "OFF") {
							if ($auto_status != "M") {
								if ($datalastposition == $street_nameportANTBIRfix) {
										$datainport += 1;
								}
							}
						}
					}
			}
		}
	}

	// DATA IN POOL
	$datapool  = array();
	for ($i=0; $i < sizeof($getallcompany); $i++) {
		$getChild      = $this->m_poipoolmaster->getStreetByParent($getallcompany[$i]['company_id']);
			for ($j=0; $j < sizeof($getChild); $j++) {
				$child_name = explode(",", $getChild[$j]['street_name']);
				array_push($datapool, array(
					$getallcompany[$i]['company_name'] => $getallcompany[$i]['company_name'].'|'.$child_name[0]
				));
			}
	}

	for ($i=0; $i < sizeof($getallcompany); $i++) {
		$datacompany                = $getallcompany[$i]['company_name'];
		$street_namefix             = $datacompany;
		$dataState[$street_namefix] = 0;

			for ($j=0; $j < sizeof($datapool); $j++) {
				if (isset($datapool[$j][$datacompany])) {
					$datachild = explode("|", $datapool[$j][$datacompany]);

					if ($datacompany == $datachild[0]) {
							for ($k=0; $k < sizeof($masterdatavehicle); $k++) {
									$autocheck          = json_decode($masterdatavehicle[$k]['vehicle_autocheck']);
									$auto_last_position = explode(",", $autocheck->auto_last_position);
									$datalastposition   = $auto_last_position[0];
									$auto_status 		    = $autocheck->auto_status;

									if ($auto_status != "M") {
										if ($datalastposition == $datachild[1]) {
											$datainpool += 1;
										}
									}
								}
					}
				}
			}
	}

	// DATA OUT OF HAULING, DATA ALL UNIT, DATA OFFLINE VEHICLE, DATA KOSONGAN & MUATAN
	for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
		$jsonautocheck                = json_decode($masterdatavehicle[$i]['vehicle_autocheck']);
		$auto_status                  = $jsonautocheck->auto_status;
		$auto_last_hauling            = explode(",", $jsonautocheck->auto_last_hauling);
		$datalastpositionoutofhauling = $auto_last_hauling[0];

		// DATA KOSONGAN
		$auto_last_position           = explode(",", $jsonautocheck->auto_last_position);
		$jalur_name                   = $jsonautocheck->auto_last_road;
		$datalastposition             = $auto_last_position[0];

			if ($jalur_name == "kosongan") {
				if ($datalastposition == "Port BIB - Kosongan 1") {
					$datakosongan += 1;
				}elseif ($datalastposition == "Port BIB - Kosongan 2") {
					$datakosongan += 1;
				}elseif ($datalastposition == "Port BIR - Kosongan 1") {
					$datakosongan += 1;
				}elseif ($datalastposition == "Port BIR - Kosongan 2") {
					$datakosongan += 1;
				}elseif ($datalastposition == "Simpang Bayah - Kosongan") {
					$datakosongan += 1;
				}elseif ($datalastposition == "KM 1" || $datalastposition == "KM 1.5" || $datalastposition == "KM 0.5") {
					$datakosongan += 1;
				}elseif ($datalastposition == "KM 2" || $datalastposition == "KM 2.5") {
					$datakosongan += 1;
				}elseif ($datalastposition == "KM 3" || $datalastposition == "KM 3.5") {
					$datakosongan += 1;
				}elseif ($datalastposition == "KM 4" || $datalastposition == "KM 4.5") {
					$datakosongan += 1;
				}elseif ($datalastposition == "KM 5" || $datalastposition == "KM 5.5") {
					$datakosongan += 1;
				}elseif ($datalastposition == "KM 6" || $datalastposition == "KM 6.5") {
					$datakosongan += 1;
				}elseif ($datalastposition == "KM 7" || $datalastposition == "KM 7.5") {
					$datakosongan += 1;
				}elseif ($datalastposition == "KM 8" || $datalastposition == "KM 8.5") {
					$datakosongan += 1;
				}elseif ($datalastposition == "KM 9" || $datalastposition == "KM 9.5") {
					$datakosongan += 1;
				}elseif ($datalastposition == "KM 10" || $datalastposition == "KM 10.5") {
					$datakosongan += 1;
				}elseif ($datalastposition == "KM 11" || $datalastposition == "KM 11.5") {
					$datakosongan += 1;
				}elseif ($datalastposition == "KM 12" || $datalastposition == "KM 12.5") {
					$datakosongan += 1;
				}elseif ($datalastposition == "KM 13" || $datalastposition == "KM 13.5") {
					$datakosongan += 1;
				}elseif ($datalastposition == "KM 14" || $datalastposition == "KM 14.5") {
					$datakosongan += 1;
				}elseif ($datalastposition == "KM 15" || $datalastposition == "KM 15.5") {
					$datakosongan += 1;
				}elseif ($datalastposition == "KM 16" || $datalastposition == "KM 16.5") {
					$datakosongan += 1;
				}elseif ($datalastposition == "KM 17" || $datalastposition == "KM 17.5") {
					$datakosongan += 1;
				}elseif ($datalastposition == "KM 18" || $datalastposition == "KM 18.5") {
					$datakosongan += 1;
				}elseif ($datalastposition == "KM 19" || $datalastposition == "KM 19.5") {
					$datakosongan += 1;
				}elseif ($datalastposition == "KM 20" || $datalastposition == "KM 20.5") {
					$datakosongan += 1;
				}elseif ($datalastposition == "KM 21" || $datalastposition == "KM 21.5") {
					$datakosongan += 1;
				}elseif ($datalastposition == "KM 22" || $datalastposition == "KM 22.5") {
					$datakosongan += 1;
				}elseif ($datalastposition == "KM 23" || $datalastposition == "KM 23.5") {
					$datakosongan += 1;
				}elseif ($datalastposition == "KM 24" || $datalastposition == "KM 24.5") {
					$datakosongan += 1;
				}elseif ($datalastposition == "KM 25" || $datalastposition == "KM 25.5") {
					$datakosongan += 1;
				}elseif ($datalastposition == "KM 26" || $datalastposition == "KM 26.5") {
					$datakosongan += 1;
				}elseif ($datalastposition == "KM 27" || $datalastposition == "KM 27.5") {
					$datakosongan += 1;
				}elseif ($datalastposition == "KM 28" || $datalastposition == "KM 28.5") {
					$datakosongan += 1;
				}elseif ($datalastposition == "KM 29" || $datalastposition == "KM 29.5") {
					$datakosongan += 1;
				}
			}else if($jalur_name == "muatan") {
				if ($datalastposition == "Port BIB - Antrian") {
						$datamuatan += 1;
				}elseif ($datalastposition == "Port BIR - Antrian WB") {
						$datamuatan += 1;
				}elseif ($datalastposition == "KM 1" || $datalastposition == "KM 1.5" || $datalastposition == "KM 0.5") {
						$datamuatan += 1;
				}elseif ($datalastposition == "KM 2" || $datalastposition == "KM 2.5") {
						$datamuatan += 1;
				}elseif ($datalastposition == "KM 3" || $datalastposition == "KM 3.5") {
						$datamuatan += 1;
				}elseif ($datalastposition == "KM 4" || $datalastposition == "KM 4.5") {
						$datamuatan += 1;
				}elseif ($datalastposition == "KM 5" || $datalastposition == "KM 5.5") {
						$datamuatan += 1;
				}elseif ($datalastposition == "KM 6" || $datalastposition == "KM 6.5") {
						$datamuatan += 1;
				}elseif ($datalastposition == "KM 7" || $datalastposition == "KM 7.5") {
						$datamuatan += 1;
				}elseif ($datalastposition == "KM 8" || $datalastposition == "KM 8.5") {
						$datamuatan += 1;
				}elseif ($datalastposition == "KM 9" || $datalastposition == "KM 9.5") {
						$datamuatan += 1;
				}elseif ($datalastposition == "KM 10" || $datalastposition == "KM 10.5") {
						$datamuatan += 1;
				}elseif ($datalastposition == "KM 11" || $datalastposition == "KM 11.5") {
						$datamuatan += 1;
				}elseif ($datalastposition == "KM 12" || $datalastposition == "KM 12.5") {
						$datamuatan += 1;
				}elseif ($datalastposition == "KM 13" || $datalastposition == "KM 13.5") {
						$datamuatan += 1;
				}elseif ($datalastposition == "KM 14" || $datalastposition == "KM 14.5") {
						$datamuatan += 1;
				}elseif ($datalastposition == "KM 15" || $datalastposition == "KM 15.5") {
						$datamuatan += 1;
				}elseif ($datalastposition == "KM 16" || $datalastposition == "KM 16.5") {
						$datamuatan += 1;
				}elseif ($datalastposition == "KM 17" || $datalastposition == "KM 17.5") {
						$datamuatan += 1;
				}elseif ($datalastposition == "KM 18" || $datalastposition == "KM 18.5") {
						$datamuatan += 1;
				}elseif ($datalastposition == "KM 19" || $datalastposition == "KM 19.5") {
						$datamuatan += 1;
				}elseif ($datalastposition == "KM 20" || $datalastposition == "KM 20.5") {
						$datamuatan += 1;
				}elseif ($datalastposition == "KM 21" || $datalastposition == "KM 21.5") {
						$datamuatan += 1;
				}elseif ($datalastposition == "KM 22" || $datalastposition == "KM 22.5") {
						$datamuatan += 1;
				}elseif ($datalastposition == "KM 23" || $datalastposition == "KM 23.5") {
						$datamuatan += 1;
				}elseif ($datalastposition == "KM 24" || $datalastposition == "KM 24.5") {
						$datamuatan += 1;
				}elseif ($datalastposition == "KM 25" || $datalastposition == "KM 25.5") {
						$datamuatan += 1;
				}elseif ($datalastposition == "KM 26" || $datalastposition == "KM 26.5") {
						$datamuatan += 1;
				}elseif ($datalastposition == "KM 27" || $datalastposition == "KM 27.5") {
						$datamuatan += 1;
				}elseif ($datalastposition == "KM 28" || $datalastposition == "KM 28.5") {
						$datamuatan += 1;
				}elseif ($datalastposition == "KM 29" || $datalastposition == "KM 29.5") {
						$datamuatan += 1;
				}
			}

			// DATA OUT OF HAULING
			if ($datalastpositionoutofhauling == "out") {
				$dataoutofhauling += 1;
			}

			// DATA OFFLINE VEHICLE
			if ($auto_status == "M") {
				$dataofflinevehicle += 1;
			}

		if ($company_id != 0) {
			if ($company_id == $masterdatavehicle[$i]['vehicle_company']) {
				if ($summary_mapsoption == 0) {
						array_push($dataallunit, array(
							"engine_status" 				 => "all",
						));
				}elseif ($summary_mapsoption == 1) {
						if ($jsonautocheck->auto_last_engine == "ON") {
								array_push($dataallunit, array(
									"engine_status" 				 => $jsonautocheck->auto_last_engine,
								));
						}
					}else {
						if ($jsonautocheck->auto_last_engine == "OFF") {
								array_push($dataallunit, array(
									"engine_status" 				 => $jsonautocheck->auto_last_engine,
								));
						}
					}
			}
		}else {
			if ($summary_mapsoption == 0) {
					array_push($dataallunit, array(
						"engine_status" 				 => "all",
					));
			}elseif ($summary_mapsoption == 1) {
					if ($jsonautocheck->auto_last_engine == "ON") {
							array_push($dataallunit, array(
								"engine_status" 				 => $jsonautocheck->auto_last_engine,
							));
					}
				}else {
					if ($jsonautocheck->auto_last_engine == "OFF") {
							array_push($dataallunit, array(
								"engine_status" 				 => $jsonautocheck->auto_last_engine,
							));
					}
				}
		}
	}

	// echo "<pre>";
	// var_dump($dataallunit);die();
	// echo "<pre>";

	echo json_encode(
		array(
			"totalunit"           => sizeof($dataallunit),
			"totalinrom"          => $datainrom,
			"totalinport"         => $datainport,
			"totaloutofhauling"   => $dataoutofhauling,
			"totalofflinevehicle" => $dataofflinevehicle,
			"totalinpool" 				=> $datainpool,
			"totalkosongan"       => $datakosongan,
			"totalmuatan"         => $datamuatan,
		));
}

function getSummaryTotalUnit(){
	$summary_mapsoption     = $_POST['summary_mapsoption'];
	$company_id 			      = $_POST['contractor'];
	$allCompany 					  = $this->m_poipoolmaster->getAllCompany();
	$masterdatavehicle      = $this->m_poipoolmaster->getmastervehiclebycontractor($company_id);
	$dataallunit            = array();
	$dataContractor['BKAE'] = 0;
	$dataContractor['KMB']  = 0;
	$dataContractor['GECL'] = 0;
	$dataContractor['STLI'] = 0;
	$dataContractor['RAMB'] = 0;
	$dataContractor['BBS']  = 0;
	$dataContractor['MKS']  = 0;
	$dataContractor['RBT']  = 0;
	$dataContractor['MMS']  = 0;
	$dataContractor['EST']  = 0;

	for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
		$jsonautocheck                = json_decode($masterdatavehicle[$i]['vehicle_autocheck']);
		$auto_status                  = $jsonautocheck->auto_status;
		$auto_last_hauling            = explode(",", $jsonautocheck->auto_last_hauling);
		$datalastpositionoutofhauling = $auto_last_hauling[0];
		$auto_last_position           = explode(",", $jsonautocheck->auto_last_position);
		$datalastposition             = $auto_last_position[0];
		$auto_last_engine 	          = $jsonautocheck->auto_last_engine;
		$auto_last_speed 	            = $jsonautocheck->auto_last_speed;

		if ($company_id != 0) {
			if ($company_id == $masterdatavehicle[$i]['vehicle_company']) {
				if ($summary_mapsoption == 0) {
						array_push($dataallunit, array(
							"engine_status" 				 => "all",
							"vehicle_no"             => $masterdatavehicle[$i]['vehicle_no'],
							"vehicle_name"           => $masterdatavehicle[$i]['vehicle_name'],
							"vehicle_company"        => $masterdatavehicle[$i]['vehicle_company'],
							"vehicle_position"       => $datalastposition,
							"auto_last_lat"          => $jsonautocheck->auto_last_lat,
							"auto_last_long"         => $jsonautocheck->auto_last_long,
							"auto_last_engine"       => $jsonautocheck->auto_last_engine,
							"auto_last_speed"        => $jsonautocheck->auto_last_speed,
							"auto_last_update" => date("d-m-Y H:i:s", strtotime($jsonautocheck->auto_last_update))
						));
				}elseif ($summary_mapsoption == 1) {
						if ($jsonautocheck->auto_last_engine == "ON") {
							array_push($dataallunit, array(
								"engine_status" 				 => "ON",
								"vehicle_no"             => $masterdatavehicle[$i]['vehicle_no'],
								"vehicle_name"           => $masterdatavehicle[$i]['vehicle_name'],
								"vehicle_company"        => $masterdatavehicle[$i]['vehicle_company'],
								"vehicle_position"       => $datalastposition,
								"auto_last_lat"          => $jsonautocheck->auto_last_lat,
								"auto_last_long"         => $jsonautocheck->auto_last_long,
								"auto_last_engine"       => $jsonautocheck->auto_last_engine,
								"auto_last_speed"        => $jsonautocheck->auto_last_speed,
								"auto_last_update" => date("d-m-Y H:i:s", strtotime($jsonautocheck->auto_last_update))
							));
						}
					}else {
						if ($jsonautocheck->auto_last_engine == "OFF") {
							array_push($dataallunit, array(
								"engine_status" 				 => "OFF",
								"vehicle_no"             => $masterdatavehicle[$i]['vehicle_no'],
								"vehicle_name"           => $masterdatavehicle[$i]['vehicle_name'],
								"vehicle_company"        => $masterdatavehicle[$i]['vehicle_company'],
								"vehicle_position"       => $datalastposition,
								"auto_last_lat"          => $jsonautocheck->auto_last_lat,
								"auto_last_long"         => $jsonautocheck->auto_last_long,
								"auto_last_engine"       => $jsonautocheck->auto_last_engine,
								"auto_last_speed"        => $jsonautocheck->auto_last_speed,
								"auto_last_update" => date("d-m-Y H:i:s", strtotime($jsonautocheck->auto_last_update))
							));
						}
					}
			}
		}else {
			if ($summary_mapsoption == 0) {
				array_push($dataallunit, array(
					"engine_status" 				 => "all",
					"vehicle_no"             => $masterdatavehicle[$i]['vehicle_no'],
					"vehicle_name"           => $masterdatavehicle[$i]['vehicle_name'],
					"vehicle_company"        => $masterdatavehicle[$i]['vehicle_company'],
					"vehicle_position"       => $datalastposition,
					"auto_last_lat"          => $jsonautocheck->auto_last_lat,
					"auto_last_long"         => $jsonautocheck->auto_last_long,
					"auto_last_engine"       => $jsonautocheck->auto_last_engine,
					"auto_last_speed"        => $jsonautocheck->auto_last_speed,
					"auto_last_update" => date("d-m-Y H:i:s", strtotime($jsonautocheck->auto_last_update))
				));
			}elseif ($summary_mapsoption == 1) {
					if ($jsonautocheck->auto_last_engine == "ON") {
						array_push($dataallunit, array(
							"engine_status" 				 => "ON",
							"vehicle_no"             => $masterdatavehicle[$i]['vehicle_no'],
							"vehicle_name"           => $masterdatavehicle[$i]['vehicle_name'],
							"vehicle_company"        => $masterdatavehicle[$i]['vehicle_company'],
							"vehicle_position"       => $datalastposition,
							"auto_last_lat"          => $jsonautocheck->auto_last_lat,
							"auto_last_long"         => $jsonautocheck->auto_last_long,
							"auto_last_engine"       => $jsonautocheck->auto_last_engine,
							"auto_last_speed"        => $jsonautocheck->auto_last_speed,
							"auto_last_update" => date("d-m-Y H:i:s", strtotime($jsonautocheck->auto_last_update))
						));
					}
				}else {
					if ($jsonautocheck->auto_last_engine == "OFF") {
						array_push($dataallunit, array(
							"engine_status" 				 => "OFF",
							"vehicle_no"             => $masterdatavehicle[$i]['vehicle_no'],
							"vehicle_name"           => $masterdatavehicle[$i]['vehicle_name'],
							"vehicle_company"        => $masterdatavehicle[$i]['vehicle_company'],
							"vehicle_position"       => $datalastposition,
							"auto_last_lat"          => $jsonautocheck->auto_last_lat,
							"auto_last_long"         => $jsonautocheck->auto_last_long,
							"auto_last_engine"       => $jsonautocheck->auto_last_engine,
							"auto_last_speed"        => $jsonautocheck->auto_last_speed,
							"auto_last_update" => date("d-m-Y H:i:s", strtotime($jsonautocheck->auto_last_update))
						));
					}
				}
		}
	}

	for ($k=0; $k < sizeof($dataallunit); $k++) {
		for ($j=0; $j < sizeof($allCompany); $j++) {
			if ($dataallunit[$k]['vehicle_company'] == $allCompany[$j]['company_id']) {
				if ($allCompany[$j]['company_name'] == "BKAE") {
					$dataContractor['BKAE'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "KMB") {
					$dataContractor['KMB'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "GECL") {
					$dataContractor['GECL'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "STLI") {
					$dataContractor['STLI'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "RAMB") {
					$dataContractor['RAMB'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "BBS") {
					$dataContractor['BBS'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "MKS") {
					$dataContractor['MKS'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "RBT") {
					$dataContractor['RBT'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "MMS") {
					$dataContractor['MMS'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "EST") {
					$dataContractor['EST'] += 1;
				}
			}
		}
	}

	// echo "<pre>";
	// var_dump($dataallunit);die();
	// echo "<pre>";

	echo json_encode(
		array(
			"data" 		          => $dataallunit,
			"totalunit" 		    => sizeof($dataallunit),
			"jumlah_contractor" => $dataContractor
		));
}

function getSummaryTotalUnitInRom(){
	$summary_mapsoption     = $_POST['summary_mapsoption'];
	$company_id 			      = $_POST['contractor'];
	$typeofstreetrom		    = $_POST['typeofstreetrom'];
	$allCompany 					  = $this->m_poipoolmaster->getAllCompany();
	$masterdatavehicle      = $this->m_poipoolmaster->getmastervehiclebycontractor($company_id);
	$streetRom              = $this->m_poipoolmaster->getstreet_now($typeofstreetrom);
	$jumlahDataInRom        = array();
	$dataContractor['BKAE'] = 0;
	$dataContractor['KMB']  = 0;
	$dataContractor['GECL'] = 0;
	$dataContractor['STLI'] = 0;
	$dataContractor['RAMB'] = 0;
	$dataContractor['BBS']  = 0;
	$dataContractor['MKS']  = 0;
	$dataContractor['RBT']  = 0;
	$dataContractor['MMS']  = 0;
	$dataContractor['EST']  = 0;

	for ($j=0; $j < sizeof($streetRom); $j++) {
		$street_name                = explode(",", $streetRom[$j]['street_name']);
		$street_namefix             = $street_name[0];
		$dataState[$street_namefix] = 0;

		for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
			$jsonautocheck      = json_decode($masterdatavehicle[$i]['vehicle_autocheck']);
			$auto_last_position = explode(",", $jsonautocheck->auto_last_position);
			$datalastposition   = $auto_last_position[0];
			$auto_status 		    = $jsonautocheck->auto_status;

			if ($company_id != 0) {
				if ($summary_mapsoption == 0) {
					if ($auto_status != "M") {
						if ($datalastposition == $street_namefix) {
							array_push($jumlahDataInRom, array(
								"engine_status" 				 => "all",
								"vehicle_no"             => $masterdatavehicle[$i]['vehicle_no'],
								"vehicle_name"           => $masterdatavehicle[$i]['vehicle_name'],
								"vehicle_company"        => $masterdatavehicle[$i]['vehicle_company'],
								"vehicle_position"       => $datalastposition,
								"auto_last_lat"          => $jsonautocheck->auto_last_lat,
								"auto_last_long"         => $jsonautocheck->auto_last_long,
								"auto_last_engine"       => $jsonautocheck->auto_last_engine,
								"auto_last_speed"        => $jsonautocheck->auto_last_speed,
								"auto_last_update" => date("d-m-Y H:i:s", strtotime($jsonautocheck->auto_last_update))
							));
						}
					}
				}elseif ($summary_mapsoption == 1) {
						if ($jsonautocheck->auto_last_engine == "ON") {
							if ($auto_status != "M") {
								if ($datalastposition == $street_namefix) {
									array_push($jumlahDataInRom, array(
										"engine_status" 				 => "ON",
										"vehicle_no"             => $masterdatavehicle[$i]['vehicle_no'],
										"vehicle_name"           => $masterdatavehicle[$i]['vehicle_name'],
										"vehicle_company"        => $masterdatavehicle[$i]['vehicle_company'],
										"vehicle_position"       => $datalastposition,
										"auto_last_lat"          => $jsonautocheck->auto_last_lat,
										"auto_last_long"         => $jsonautocheck->auto_last_long,
										"auto_last_engine"       => $jsonautocheck->auto_last_engine,
										"auto_last_speed"        => $jsonautocheck->auto_last_speed,
										"auto_last_update" => date("d-m-Y H:i:s", strtotime($jsonautocheck->auto_last_update))
									));
								}
							}
						}
					}else {
						if ($jsonautocheck->auto_last_engine == "OFF") {
							if ($auto_status != "M") {
								if ($datalastposition == $street_namefix) {
									array_push($jumlahDataInRom, array(
										"engine_status" 				 => "OFF",
										"vehicle_no"             => $masterdatavehicle[$i]['vehicle_no'],
										"vehicle_name"           => $masterdatavehicle[$i]['vehicle_name'],
										"vehicle_company"        => $masterdatavehicle[$i]['vehicle_company'],
										"vehicle_position"       => $datalastposition,
										"auto_last_lat"          => $jsonautocheck->auto_last_lat,
										"auto_last_long"         => $jsonautocheck->auto_last_long,
										"auto_last_engine"       => $jsonautocheck->auto_last_engine,
										"auto_last_speed"        => $jsonautocheck->auto_last_speed,
										"auto_last_update" => date("d-m-Y H:i:s", strtotime($jsonautocheck->auto_last_update))
									));
								}
							}
						}
					}
			}else {
				if ($summary_mapsoption == 0) {
					if ($auto_status != "M") {
						if ($datalastposition == $street_namefix) {
							array_push($jumlahDataInRom, array(
								"engine_status" 				 => "all",
								"vehicle_no"             => $masterdatavehicle[$i]['vehicle_no'],
								"vehicle_name"           => $masterdatavehicle[$i]['vehicle_name'],
								"vehicle_company"        => $masterdatavehicle[$i]['vehicle_company'],
								"vehicle_position"       => $datalastposition,
								"auto_last_lat"          => $jsonautocheck->auto_last_lat,
								"auto_last_long"         => $jsonautocheck->auto_last_long,
								"auto_last_engine"       => $jsonautocheck->auto_last_engine,
								"auto_last_speed"        => $jsonautocheck->auto_last_speed,
								"auto_last_update" => date("d-m-Y H:i:s", strtotime($jsonautocheck->auto_last_update))
							));
						}
					}
				}elseif ($summary_mapsoption == 1) {
						if ($jsonautocheck->auto_last_engine == "ON") {
							if ($auto_status != "M") {
								if ($datalastposition == $street_namefix) {
									array_push($jumlahDataInRom, array(
										"engine_status" 				 => "ON",
										"vehicle_no"             => $masterdatavehicle[$i]['vehicle_no'],
										"vehicle_name"           => $masterdatavehicle[$i]['vehicle_name'],
										"vehicle_company"        => $masterdatavehicle[$i]['vehicle_company'],
										"vehicle_position"       => $datalastposition,
										"auto_last_lat"          => $jsonautocheck->auto_last_lat,
										"auto_last_long"         => $jsonautocheck->auto_last_long,
										"auto_last_engine"       => $jsonautocheck->auto_last_engine,
										"auto_last_speed"        => $jsonautocheck->auto_last_speed,
										"auto_last_update" => date("d-m-Y H:i:s", strtotime($jsonautocheck->auto_last_update))
									));
								}
							}
						}
					}else {
						if ($jsonautocheck->auto_last_engine == "OFF") {
							if ($auto_status != "M") {
								if ($datalastposition == $street_namefix) {
									array_push($jumlahDataInRom, array(
										"engine_status" 				 => "OFF",
										"vehicle_no"             => $masterdatavehicle[$i]['vehicle_no'],
										"vehicle_name"           => $masterdatavehicle[$i]['vehicle_name'],
										"vehicle_company"        => $masterdatavehicle[$i]['vehicle_company'],
										"vehicle_position"       => $datalastposition,
										"auto_last_lat"          => $jsonautocheck->auto_last_lat,
										"auto_last_long"         => $jsonautocheck->auto_last_long,
										"auto_last_engine"       => $jsonautocheck->auto_last_engine,
										"auto_last_speed"        => $jsonautocheck->auto_last_speed,
										"auto_last_update" => date("d-m-Y H:i:s", strtotime($jsonautocheck->auto_last_update))
									));
								}
							}
						}
					}
			}
		}
	}

	for ($k=0; $k < sizeof($jumlahDataInRom); $k++) {
		for ($j=0; $j < sizeof($allCompany); $j++) {
			if ($jumlahDataInRom[$k]['vehicle_company'] == $allCompany[$j]['company_id']) {
				if ($allCompany[$j]['company_name'] == "BKAE") {
					$dataContractor['BKAE'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "KMB") {
					$dataContractor['KMB'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "GECL") {
					$dataContractor['GECL'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "STLI") {
					$dataContractor['STLI'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "RAMB") {
					$dataContractor['RAMB'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "BBS") {
					$dataContractor['BBS'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "MKS") {
					$dataContractor['MKS'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "RBT") {
					$dataContractor['RBT'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "MMS") {
					$dataContractor['MMS'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "EST") {
					$dataContractor['EST'] += 1;
				}
			}
		}
	}

	echo json_encode(
		array(
			"data" 		          => $jumlahDataInRom,
			"totalunit" 		    => sizeof($jumlahDataInRom),
			"jumlah_contractor" => $dataContractor
		));
}

function getSummaryTotalUnitInPort(){
	$summary_mapsoption     = $_POST['summary_mapsoption'];
	$company_id 			      = $_POST['contractor'];
	$typeofstreetport		    = $_POST['typeofstreetport'];
	$allCompany 					  = $this->m_poipoolmaster->getAllCompany();
	$masterdatavehicle      = $this->m_poipoolmaster->getmastervehiclebycontractor($company_id);
	$streetPort             = $this->m_poipoolmaster->getstreet_now($typeofstreetport);
	$jumlahDataInPort       = array();
	$dataContractor['BKAE'] = 0;
	$dataContractor['KMB']  = 0;
	$dataContractor['GECL'] = 0;
	$dataContractor['STLI'] = 0;
	$dataContractor['RAMB'] = 0;
	$dataContractor['BBS']  = 0;
	$dataContractor['MKS']  = 0;
	$dataContractor['RBT']  = 0;
	$dataContractor['MMS']  = 0;
	$dataContractor['EST']  = 0;

	for ($j=0; $j < sizeof($streetPort); $j++) {
		$street_name                = explode(",", $streetPort[$j]['street_name']);
		$street_namefix             = $street_name[0];
		$dataState[$street_namefix] = 0;

		for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
			$jsonautocheck      = json_decode($masterdatavehicle[$i]['vehicle_autocheck']);
			$auto_last_position = explode(",", $jsonautocheck->auto_last_position);
			$datalastposition   = $auto_last_position[0];
			$auto_status 		    = $jsonautocheck->auto_status;

			if ($company_id != 0) {
				if ($summary_mapsoption == 0) {
					if ($auto_status != "M") {
						if ($datalastposition == $street_namefix) {
							array_push($jumlahDataInPort, array(
								"engine_status" 				 => "all",
								"vehicle_no"             => $masterdatavehicle[$i]['vehicle_no'],
								"vehicle_name"           => $masterdatavehicle[$i]['vehicle_name'],
								"vehicle_company"        => $masterdatavehicle[$i]['vehicle_company'],
								"vehicle_position"       => $datalastposition,
								"auto_last_lat"          => $jsonautocheck->auto_last_lat,
								"auto_last_long"         => $jsonautocheck->auto_last_long,
								"auto_last_engine"       => $jsonautocheck->auto_last_engine,
								"auto_last_speed"        => $jsonautocheck->auto_last_speed,
								"auto_last_update" => date("d-m-Y H:i:s", strtotime($jsonautocheck->auto_last_update))
							));
						}
					}
				}elseif ($summary_mapsoption == 1) {
						if ($jsonautocheck->auto_last_engine == "ON") {
							if ($auto_status != "M") {
								if ($datalastposition == $street_namefix) {
									array_push($jumlahDataInPort, array(
										"engine_status" 				 => "ON",
										"vehicle_no"             => $masterdatavehicle[$i]['vehicle_no'],
										"vehicle_name"           => $masterdatavehicle[$i]['vehicle_name'],
										"vehicle_company"        => $masterdatavehicle[$i]['vehicle_company'],
										"vehicle_position"       => $datalastposition,
										"auto_last_lat"          => $jsonautocheck->auto_last_lat,
										"auto_last_long"         => $jsonautocheck->auto_last_long,
										"auto_last_engine"       => $jsonautocheck->auto_last_engine,
										"auto_last_speed"        => $jsonautocheck->auto_last_speed,
										"auto_last_update" => date("d-m-Y H:i:s", strtotime($jsonautocheck->auto_last_update))
									));
								}
							}
						}
					}else {
						if ($jsonautocheck->auto_last_engine == "OFF") {
							if ($auto_status != "M") {
								if ($datalastposition == $street_namefix) {
									array_push($jumlahDataInPort, array(
										"engine_status" 				 => "OFF",
										"vehicle_no"             => $masterdatavehicle[$i]['vehicle_no'],
										"vehicle_name"           => $masterdatavehicle[$i]['vehicle_name'],
										"vehicle_company"        => $masterdatavehicle[$i]['vehicle_company'],
										"vehicle_position"       => $datalastposition,
										"auto_last_lat"          => $jsonautocheck->auto_last_lat,
										"auto_last_long"         => $jsonautocheck->auto_last_long,
										"auto_last_engine"       => $jsonautocheck->auto_last_engine,
										"auto_last_speed"        => $jsonautocheck->auto_last_speed,
										"auto_last_update" => date("d-m-Y H:i:s", strtotime($jsonautocheck->auto_last_update))
									));
								}
							}
						}
					}
			}else {
				if ($summary_mapsoption == 0) {
					if ($auto_status != "M") {
						if ($datalastposition == $street_namefix) {
							array_push($jumlahDataInPort, array(
								"engine_status" 				 => "all",
								"vehicle_no"             => $masterdatavehicle[$i]['vehicle_no'],
								"vehicle_name"           => $masterdatavehicle[$i]['vehicle_name'],
								"vehicle_company"        => $masterdatavehicle[$i]['vehicle_company'],
								"vehicle_position"       => $datalastposition,
								"auto_last_lat"          => $jsonautocheck->auto_last_lat,
								"auto_last_long"         => $jsonautocheck->auto_last_long,
								"auto_last_engine"       => $jsonautocheck->auto_last_engine,
								"auto_last_speed"        => $jsonautocheck->auto_last_speed,
								"auto_last_update" => date("d-m-Y H:i:s", strtotime($jsonautocheck->auto_last_update))
							));
						}
					}
				}elseif ($summary_mapsoption == 1) {
						if ($jsonautocheck->auto_last_engine == "ON") {
							if ($auto_status != "M") {
								if ($datalastposition == $street_namefix) {
									array_push($jumlahDataInPort, array(
										"engine_status" 				 => "ON",
										"vehicle_no"             => $masterdatavehicle[$i]['vehicle_no'],
										"vehicle_name"           => $masterdatavehicle[$i]['vehicle_name'],
										"vehicle_company"        => $masterdatavehicle[$i]['vehicle_company'],
										"vehicle_position"       => $datalastposition,
										"auto_last_lat"          => $jsonautocheck->auto_last_lat,
										"auto_last_long"         => $jsonautocheck->auto_last_long,
										"auto_last_engine"       => $jsonautocheck->auto_last_engine,
										"auto_last_speed"        => $jsonautocheck->auto_last_speed,
										"auto_last_update" => date("d-m-Y H:i:s", strtotime($jsonautocheck->auto_last_update))
									));
								}
							}
						}
					}else {
						if ($jsonautocheck->auto_last_engine == "OFF") {
							if ($auto_status != "M") {
								if ($datalastposition == $street_namefix) {
									array_push($jumlahDataInPort, array(
										"engine_status" 				 => "OFF",
										"vehicle_no"             => $masterdatavehicle[$i]['vehicle_no'],
										"vehicle_name"           => $masterdatavehicle[$i]['vehicle_name'],
										"vehicle_company"        => $masterdatavehicle[$i]['vehicle_company'],
										"vehicle_position"       => $datalastposition,
										"auto_last_lat"          => $jsonautocheck->auto_last_lat,
										"auto_last_long"         => $jsonautocheck->auto_last_long,
										"auto_last_engine"       => $jsonautocheck->auto_last_engine,
										"auto_last_speed"        => $jsonautocheck->auto_last_speed,
										"auto_last_update" => date("d-m-Y H:i:s", strtotime($jsonautocheck->auto_last_update))
									));
								}
							}
						}
					}
			}
		}
	}

	for ($k=0; $k < sizeof($jumlahDataInPort); $k++) {
		for ($j=0; $j < sizeof($allCompany); $j++) {
			if ($jumlahDataInPort[$k]['vehicle_company'] == $allCompany[$j]['company_id']) {
				if ($allCompany[$j]['company_name'] == "BKAE") {
					$dataContractor['BKAE'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "KMB") {
					$dataContractor['KMB'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "GECL") {
					$dataContractor['GECL'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "STLI") {
					$dataContractor['STLI'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "RAMB") {
					$dataContractor['RAMB'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "BBS") {
					$dataContractor['BBS'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "MKS") {
					$dataContractor['MKS'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "RBT") {
					$dataContractor['RBT'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "MMS") {
					$dataContractor['MMS'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "EST") {
					$dataContractor['EST'] += 1;
				}
			}
		}
	}

	echo json_encode(
		array(
			"data" 		          => $jumlahDataInPort,
			"totalunit" 		    => sizeof($jumlahDataInPort),
			"jumlah_contractor" => $dataContractor
		));
}

function getSummaryTotalUnitInPool(){
	$summary_mapsoption          = $_POST['summary_mapsoption'];
	$company_id 			           = $_POST['contractor'];
	$getallcompany 							 = $this->m_poipoolmaster->getAllCompany();
	$masterdatavehicle           = $this->m_poipoolmaster->getmastervehiclebycontractor($company_id);
	$jumlahDataInPool            = array();
	$dataContractor['BKAE']      = 0;
	$dataContractor['KMB']       = 0;
	$dataContractor['GECL']      = 0;
	$dataContractor['STLI']      = 0;
	$dataContractor['RAMB']      = 0;
	$dataContractor['BBS']       = 0;
	$dataContractor['MKS']       = 0;
	$dataContractor['RBT']       = 0;
	$dataContractor['MMS']       = 0;
	$dataContractor['EST']       = 0;

	$datapool   = array();
	$datainpool = array();

	for ($i=0; $i < sizeof($getallcompany); $i++) {
		$getChild      = $this->m_poipoolmaster->getStreetByParent($getallcompany[$i]['company_id']);
			for ($j=0; $j < sizeof($getChild); $j++) {
				$child_name = explode(",", $getChild[$j]['street_name']);
				array_push($datapool, array(
					$getallcompany[$i]['company_name'] => $getallcompany[$i]['company_name'].'|'.$child_name[0]
				));
			}
	}

	for ($i=0; $i < sizeof($getallcompany); $i++) {
		$datacompany                = $getallcompany[$i]['company_name'];
		$street_namefix             = $datacompany;

			for ($j=0; $j < sizeof($datapool); $j++) {
				if (isset($datapool[$j][$datacompany])) {
					$datachild = explode("|", $datapool[$j][$datacompany]);

					if ($datacompany == $datachild[0]) {
							for ($k=0; $k < sizeof($masterdatavehicle); $k++) {
									$jsonautocheck      = json_decode($masterdatavehicle[$k]['vehicle_autocheck']);
									$auto_last_position = explode(",", $jsonautocheck->auto_last_position);
									$datalastposition   = $auto_last_position[0];
									$auto_status 		    = $jsonautocheck->auto_status;

									if ($summary_mapsoption == 0) {
										if ($auto_status != "M") {
											if ($datalastposition == $datachild[1]) {
												array_push($datainpool, array(
													"engine_status" 				 => "all",
													"vehicle_no"             => $masterdatavehicle[$k]['vehicle_no'],
													"vehicle_name"           => $masterdatavehicle[$k]['vehicle_name'],
													"vehicle_company"        => $masterdatavehicle[$k]['vehicle_company'],
													"vehicle_position"       => $datalastposition,
													"auto_last_lat"          => $jsonautocheck->auto_last_lat,
													"auto_last_long"         => $jsonautocheck->auto_last_long,
													"auto_last_engine"       => $jsonautocheck->auto_last_engine,
													"auto_last_speed"        => $jsonautocheck->auto_last_speed,
													"auto_last_update"       => date("d-m-Y H:i:s", strtotime($jsonautocheck->auto_last_update))
												));
											}
										}
									}elseif ($summary_mapsoption == 1) {
											if ($jsonautocheck->auto_last_engine == "ON") {
												if ($auto_status != "M") {
													if ($datalastposition == $datachild[1]) {
														array_push($datainpool, array(
															"engine_status" 				 => "ON",
															"vehicle_no"             => $masterdatavehicle[$k]['vehicle_no'],
															"vehicle_name"           => $masterdatavehicle[$k]['vehicle_name'],
															"vehicle_company"        => $masterdatavehicle[$k]['vehicle_company'],
															"vehicle_position"       => $datalastposition,
															"auto_last_lat"          => $jsonautocheck->auto_last_lat,
															"auto_last_long"         => $jsonautocheck->auto_last_long,
															"auto_last_engine"       => $jsonautocheck->auto_last_engine,
															"auto_last_speed"        => $jsonautocheck->auto_last_speed,
															"auto_last_update"       => date("d-m-Y H:i:s", strtotime($jsonautocheck->auto_last_update))
														));
													}
												}
											}
										}else {
											if ($jsonautocheck->auto_last_engine == "OFF") {
												if ($auto_status != "M") {
													if ($datalastposition == $datachild[1]) {
														array_push($datainpool, array(
															"engine_status" 				 => "ON",
															"vehicle_no"             => $masterdatavehicle[$k]['vehicle_no'],
															"vehicle_name"           => $masterdatavehicle[$k]['vehicle_name'],
															"vehicle_company"        => $masterdatavehicle[$k]['vehicle_company'],
															"vehicle_position"       => $datalastposition,
															"auto_last_lat"          => $jsonautocheck->auto_last_lat,
															"auto_last_long"         => $jsonautocheck->auto_last_long,
															"auto_last_engine"       => $jsonautocheck->auto_last_engine,
															"auto_last_speed"        => $jsonautocheck->auto_last_speed,
															"auto_last_update"       => date("d-m-Y H:i:s", strtotime($jsonautocheck->auto_last_update))
														));
													}
												}
											}
										}
								}
					}
				}
			}
	}

	for ($k=0; $k < sizeof($datainpool); $k++) {
		for ($j=0; $j < sizeof($getallcompany); $j++) {
			if ($datainpool[$k]['vehicle_company'] == $getallcompany[$j]['company_id']) {
				if ($getallcompany[$j]['company_name'] == "BKAE") {
					$dataContractor['BKAE'] += 1;
				}elseif ($getallcompany[$j]['company_name'] == "KMB") {
					$dataContractor['KMB'] += 1;
				}elseif ($getallcompany[$j]['company_name'] == "GECL") {
					$dataContractor['GECL'] += 1;
				}elseif ($getallcompany[$j]['company_name'] == "STLI") {
					$dataContractor['STLI'] += 1;
				}elseif ($getallcompany[$j]['company_name'] == "RAMB") {
					$dataContractor['RAMB'] += 1;
				}elseif ($getallcompany[$j]['company_name'] == "BBS") {
					$dataContractor['BBS'] += 1;
				}elseif ($getallcompany[$j]['company_name'] == "MKS") {
					$dataContractor['MKS'] += 1;
				}elseif ($getallcompany[$j]['company_name'] == "RBT") {
					$dataContractor['RBT'] += 1;
				}elseif ($getallcompany[$j]['company_name'] == "MMS") {
					$dataContractor['MMS'] += 1;
				}elseif ($getallcompany[$j]['company_name'] == "EST") {
					$dataContractor['EST'] += 1;
				}
			}
		}
	}

	// echo "<pre>";
	// var_dump($datainpool);die();
	// echo "<pre>";

	echo json_encode(
		array(
			"data" 		          => $datainpool,
			"totalunit" 		    => sizeof($datainpool),
			"jumlah_contractor" => $dataContractor
		));
}

function getsummarymapsoptionbyfilter(){
	$summary_mapsoption              = $_POST['summary_mapsoption'];
	$company_id 			               = $_POST['contractor'];
	$typeofstreetrom		             = $_POST['typeofstreetrom'];
	$typeofstreetport		             = $_POST['typeofstreetport'];
	$getallcompany    							 = $this->m_poipoolmaster->getAllCompany();
	$masterdatavehicle               = $this->m_poipoolmaster->getmastervehiclebycontractor($company_id);
	$streetRom                       = $this->m_poipoolmaster->getstreet_now($typeofstreetrom);
	$portStreet                      = $this->m_poipoolmaster->getstreet_now2($typeofstreetport);
	$portCPBIB                       = $this->m_poipoolmaster->getstreet_now2(7);
	$portANTBIR                      = $this->m_poipoolmaster->getstreet_now2(8);
	$dataallunit 						         = array();
	$datainrom                       = 0;
	$datainport                      = 0;
	$dataoutofhauling                = 0;
	$dataofflinevehicle              = 0;
	$datainpool 										 = 0;
	$datakosongan                    = 0;
	$datamuatan                      = 0;

	// DATA IN ROM
	for ($j=0; $j < sizeof($streetRom); $j++) {
		$street_name                = explode(",", $streetRom[$j]['street_name']);
		$street_namefix             = $street_name[0];
		$dataState[$street_namefix] = 0;

		for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
			$jsonautocheck      = json_decode($masterdatavehicle[$i]['vehicle_autocheck']);
			$auto_last_position = explode(",", $jsonautocheck->auto_last_position);
			$datalastposition   = $auto_last_position[0];
			$auto_status 		    = $jsonautocheck->auto_status;

			if ($company_id != 0) {
				if ($summary_mapsoption == 0) {
					if ($auto_status != "M") {
						if ($datalastposition == $street_namefix) {
								$datainrom += 1;
						}
					}
				}elseif ($summary_mapsoption == 1) {
						if ($jsonautocheck->auto_last_engine == "ON") {
							if ($auto_status != "M") {
								if ($datalastposition == $street_namefix) {
										$datainrom += 1;
								}
							}
						}
					}else {
						if ($jsonautocheck->auto_last_engine == "OFF") {
							if ($auto_status != "M") {
								if ($datalastposition == $street_namefix) {
										$datainrom += 1;
								}
							}
						}
					}
			}else {
				if ($summary_mapsoption == 0) {
					if ($auto_status != "M") {
						if ($datalastposition == $street_namefix) {
								$datainrom += 1;
						}
					}
				}elseif ($summary_mapsoption == 1) {
						if ($jsonautocheck->auto_last_engine == "ON") {
							if ($auto_status != "M") {
								if ($datalastposition == $street_namefix) {
										$datainrom += 1;
								}
							}
						}
					}else {
						if ($jsonautocheck->auto_last_engine == "OFF") {
							if ($auto_status != "M") {
								if ($datalastposition == $street_namefix) {
										$datainrom += 1;
								}
							}
						}
					}
			}
		}
	}

	// DATA IN PORT
	for ($k=0; $k < sizeof($portStreet); $k++) {
		$street_name_port                   = explode(",", $portStreet[$k]['street_name']);
		$street_nameportfix                 = $street_name_port[0];
		$dataStatePort[$street_nameportfix] = 0;

		for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
			$jsonautocheck          = json_decode($masterdatavehicle[$i]['vehicle_autocheck']);
			$auto_last_position = explode(",", $jsonautocheck->auto_last_position);
			$datalastposition   = $auto_last_position[0];
			$auto_status 		    = $jsonautocheck->auto_status;

			if ($company_id != 0) {
				if ($summary_mapsoption == 0) {
					if ($auto_status != "M") {
						if ($datalastposition == $street_nameportfix) {
								$datainport += 1;
						}
					}
				}elseif ($summary_mapsoption == 1) {
						if ($jsonautocheck->auto_last_engine == "ON") {
							if ($auto_status != "M") {
								if ($datalastposition == $street_nameportfix) {
										$datainport += 1;
								}
							}
						}
					}else {
						if ($jsonautocheck->auto_last_engine == "OFF") {
							if ($auto_status != "M") {
								if ($datalastposition == $street_nameportfix) {
										$datainport += 1;
								}
							}
						}
					}
			}else {
				if ($summary_mapsoption == 0) {
					if ($auto_status != "M") {
						if ($datalastposition == $street_nameportfix) {
								$datainport += 1;
						}
					}
				}elseif ($summary_mapsoption == 1) {
						if ($jsonautocheck->auto_last_engine == "ON") {
							if ($auto_status != "M") {
								if ($datalastposition == $street_nameportfix) {
										$datainport += 1;
								}
							}
						}
					}else {
						if ($jsonautocheck->auto_last_engine == "OFF") {
							if ($auto_status != "M") {
								if ($datalastposition == $street_nameportfix) {
										$datainport += 1;
								}
							}
						}
					}
			}
		}
	}

	for ($l=0; $l < sizeof($portCPBIB); $l++) {
		$street_name_portCPBIB                   = explode(",", $portCPBIB[$l]['street_name']);
		$street_nameportCPBIBfix                 = $street_name_portCPBIB[0];
		$dataStatePortCPBIB[$street_nameportCPBIBfix] = 0;

		for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
			$jsonautocheck          = json_decode($masterdatavehicle[$i]['vehicle_autocheck']);
			$auto_last_position = explode(",", $jsonautocheck->auto_last_position);
			$datalastposition   = $auto_last_position[0];
			$auto_status 		    = $jsonautocheck->auto_status;

			if ($company_id != 0) {
				if ($summary_mapsoption == 0) {
					if ($auto_status != "M") {
						if ($datalastposition == $street_nameportCPBIBfix) {
								$datainport += 1;
						}
					}
				}elseif ($summary_mapsoption == 1) {
						if ($jsonautocheck->auto_last_engine == "ON") {
							if ($auto_status != "M") {
								if ($datalastposition == $street_nameportCPBIBfix) {
										$datainport += 1;
								}
							}
						}
					}else {
						if ($jsonautocheck->auto_last_engine == "OFF") {
							if ($auto_status != "M") {
								if ($datalastposition == $street_nameportCPBIBfix) {
										$datainport += 1;
								}
							}
						}
					}
			}else {
				if ($summary_mapsoption == 0) {
					if ($auto_status != "M") {
						if ($datalastposition == $street_nameportCPBIBfix) {
								$datainport += 1;
						}
					}
				}elseif ($summary_mapsoption == 1) {
						if ($jsonautocheck->auto_last_engine == "ON") {
							if ($auto_status != "M") {
								if ($datalastposition == $street_nameportCPBIBfix) {
										$datainport += 1;
								}
							}
						}
					}else {
						if ($jsonautocheck->auto_last_engine == "OFF") {
							if ($auto_status != "M") {
								if ($datalastposition == $street_nameportCPBIBfix) {
										$datainport += 1;
								}
							}
						}
					}
			}
		}
	}

	for ($m=0; $m < sizeof($portANTBIR); $m++) {
		$street_name_portANTBIR                   = explode(",", $portANTBIR[$m]['street_name']);
		$street_nameportANTBIRfix                 = $street_name_portANTBIR[0];
		$dataStatePortANTBIR[$street_nameportANTBIRfix] = 0;

		for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
			$jsonautocheck      = json_decode($masterdatavehicle[$i]['vehicle_autocheck']);
			$auto_last_position = explode(",", $jsonautocheck->auto_last_position);
			$datalastposition   = $auto_last_position[0];
			$auto_status 		    = $jsonautocheck->auto_status;

			if ($company_id != 0) {
				if ($summary_mapsoption == 0) {
					if ($auto_status != "M") {
						if ($datalastposition == $street_nameportANTBIRfix) {
								$datainport += 1;
						}
					}
				}elseif ($summary_mapsoption == 1) {
						if ($jsonautocheck->auto_last_engine == "ON") {
							if ($auto_status != "M") {
								if ($datalastposition == $street_nameportANTBIRfix) {
										$datainport += 1;
								}
							}
						}
					}else {
						if ($jsonautocheck->auto_last_engine == "OFF") {
							if ($auto_status != "M") {
								if ($datalastposition == $street_nameportANTBIRfix) {
										$datainport += 1;
								}
							}
						}
					}
			}else {
				if ($summary_mapsoption == 0) {
					if ($auto_status != "M") {
						if ($datalastposition == $street_nameportANTBIRfix) {
								$datainport += 1;
						}
					}
				}elseif ($summary_mapsoption == 1) {
						if ($jsonautocheck->auto_last_engine == "ON") {
							if ($auto_status != "M") {
								if ($datalastposition == $street_nameportANTBIRfix) {
										$datainport += 1;
								}
							}
						}
					}else {
						if ($jsonautocheck->auto_last_engine == "OFF") {
							if ($auto_status != "M") {
								if ($datalastposition == $street_nameportANTBIRfix) {
										$datainport += 1;
								}
							}
						}
					}
			}
		}
	}

	// DATA IN POOL
	$datapool  = array();
	for ($i=0; $i < sizeof($getallcompany); $i++) {
		$getChild      = $this->m_poipoolmaster->getStreetByParent($getallcompany[$i]['company_id']);
			for ($j=0; $j < sizeof($getChild); $j++) {
				$child_name = explode(",", $getChild[$j]['street_name']);
				array_push($datapool, array(
					$getallcompany[$i]['company_name'] => $getallcompany[$i]['company_name'].'|'.$child_name[0]
				));
			}
	}

	for ($i=0; $i < sizeof($getallcompany); $i++) {
		$datacompany                = $getallcompany[$i]['company_name'];
		$street_namefix             = $datacompany;
		$dataState[$street_namefix] = 0;

			for ($j=0; $j < sizeof($datapool); $j++) {
				if (isset($datapool[$j][$datacompany])) {
					$datachild = explode("|", $datapool[$j][$datacompany]);

					if ($datacompany == $datachild[0]) {
							for ($k=0; $k < sizeof($masterdatavehicle); $k++) {
									$jsonautocheck      = json_decode($masterdatavehicle[$k]['vehicle_autocheck']);
									$auto_last_position = explode(",", $jsonautocheck->auto_last_position);
									$datalastposition   = $auto_last_position[0];
									$auto_status 		    = $jsonautocheck->auto_status;

									if ($company_id != 0) {
										if ($summary_mapsoption == 0) {
											if ($auto_status != "M") {
												if ($datalastposition == $datachild[1]) {
													$datainpool += 1;
												}
											}
										}elseif ($summary_mapsoption == 1) {
												if ($jsonautocheck->auto_last_engine == "ON") {
													if ($auto_status != "M") {
														if ($datalastposition == $datachild[1]) {
															$datainpool += 1;
														}
													}
												}
											}else {
												if ($jsonautocheck->auto_last_engine == "OFF") {
													if ($auto_status != "M") {
														if ($datalastposition == $datachild[1]) {
															$datainpool += 1;
														}
													}
												}
											}
									}else {
										if ($summary_mapsoption == 0) {
											if ($auto_status != "M") {
												if ($datalastposition == $datachild[1]) {
													$datainpool += 1;
												}
											}
										}elseif ($summary_mapsoption == 1) {
												if ($jsonautocheck->auto_last_engine == "ON") {
													if ($auto_status != "M") {
														if ($datalastposition == $datachild[1]) {
															$datainpool += 1;
														}
													}
												}
											}else {
												if ($jsonautocheck->auto_last_engine == "OFF") {
													if ($auto_status != "M") {
														if ($datalastposition == $datachild[1]) {
															$datainpool += 1;
														}
													}
												}
											}
									}
								}
					}
				}
			}
	}

	// DATA OUT OF HAULING, DATA ALL UNIT, DATA OFFLINE VEHICLE, DATA KOSONGAN & MUATAN
	for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
		$jsonautocheck                = json_decode($masterdatavehicle[$i]['vehicle_autocheck']);
		$auto_status                  = $jsonautocheck->auto_status;
		$auto_last_hauling            = explode(",", $jsonautocheck->auto_last_hauling);
		$datalastpositionoutofhauling = $auto_last_hauling[0];

		// DATA KOSONGAN
		$auto_last_position           = explode(",", $jsonautocheck->auto_last_position);
		$jalur_name                   = $jsonautocheck->auto_last_road;
		$datalastposition             = $auto_last_position[0];

			if ($jalur_name == "kosongan") {
				if ($summary_mapsoption == 0) {
					if ($datalastposition == "Port BIB - Kosongan 1") {
						$datakosongan += 1;
					}elseif ($datalastposition == "Port BIB - Kosongan 2") {
						$datakosongan += 1;
					}elseif ($datalastposition == "Port BIR - Kosongan 1") {
						$datakosongan += 1;
					}elseif ($datalastposition == "Port BIR - Kosongan 2") {
						$datakosongan += 1;
					}elseif ($datalastposition == "Simpang Bayah - Kosongan") {
						$datakosongan += 1;
					}elseif ($datalastposition == "KM 1" || $datalastposition == "KM 1.5" || $datalastposition == "KM 0.5") {
						$datakosongan += 1;
					}elseif ($datalastposition == "KM 2" || $datalastposition == "KM 2.5") {
						$datakosongan += 1;
					}elseif ($datalastposition == "KM 3" || $datalastposition == "KM 3.5") {
						$datakosongan += 1;
					}elseif ($datalastposition == "KM 4" || $datalastposition == "KM 4.5") {
						$datakosongan += 1;
					}elseif ($datalastposition == "KM 5" || $datalastposition == "KM 5.5") {
						$datakosongan += 1;
					}elseif ($datalastposition == "KM 6" || $datalastposition == "KM 6.5") {
						$datakosongan += 1;
					}elseif ($datalastposition == "KM 7" || $datalastposition == "KM 7.5") {
						$datakosongan += 1;
					}elseif ($datalastposition == "KM 8" || $datalastposition == "KM 8.5") {
						$datakosongan += 1;
					}elseif ($datalastposition == "KM 9" || $datalastposition == "KM 9.5") {
						$datakosongan += 1;
					}elseif ($datalastposition == "KM 10" || $datalastposition == "KM 10.5") {
						$datakosongan += 1;
					}elseif ($datalastposition == "KM 11" || $datalastposition == "KM 11.5") {
						$datakosongan += 1;
					}elseif ($datalastposition == "KM 12" || $datalastposition == "KM 12.5") {
						$datakosongan += 1;
					}elseif ($datalastposition == "KM 13" || $datalastposition == "KM 13.5") {
						$datakosongan += 1;
					}elseif ($datalastposition == "KM 14" || $datalastposition == "KM 14.5") {
						$datakosongan += 1;
					}elseif ($datalastposition == "KM 15" || $datalastposition == "KM 15.5") {
						$datakosongan += 1;
					}elseif ($datalastposition == "KM 16" || $datalastposition == "KM 16.5") {
						$datakosongan += 1;
					}elseif ($datalastposition == "KM 17" || $datalastposition == "KM 17.5") {
						$datakosongan += 1;
					}elseif ($datalastposition == "KM 18" || $datalastposition == "KM 18.5") {
						$datakosongan += 1;
					}elseif ($datalastposition == "KM 19" || $datalastposition == "KM 19.5") {
						$datakosongan += 1;
					}elseif ($datalastposition == "KM 20" || $datalastposition == "KM 20.5") {
						$datakosongan += 1;
					}elseif ($datalastposition == "KM 21" || $datalastposition == "KM 21.5") {
						$datakosongan += 1;
					}elseif ($datalastposition == "KM 22" || $datalastposition == "KM 22.5") {
						$datakosongan += 1;
					}elseif ($datalastposition == "KM 23" || $datalastposition == "KM 23.5") {
						$datakosongan += 1;
					}elseif ($datalastposition == "KM 24" || $datalastposition == "KM 24.5") {
						$datakosongan += 1;
					}elseif ($datalastposition == "KM 25" || $datalastposition == "KM 25.5") {
						$datakosongan += 1;
					}elseif ($datalastposition == "KM 26" || $datalastposition == "KM 26.5") {
						$datakosongan += 1;
					}elseif ($datalastposition == "KM 27" || $datalastposition == "KM 27.5") {
						$datakosongan += 1;
					}elseif ($datalastposition == "KM 28" || $datalastposition == "KM 28.5") {
						$datakosongan += 1;
					}elseif ($datalastposition == "KM 29" || $datalastposition == "KM 29.5") {
						$datakosongan += 1;
					}
				}elseif ($summary_mapsoption == 1) {
					if ($jsonautocheck->auto_last_engine == "ON") {
						if ($datalastposition == "Port BIB - Kosongan 1") {
							$datakosongan += 1;
						}elseif ($datalastposition == "Port BIB - Kosongan 2") {
							$datakosongan += 1;
						}elseif ($datalastposition == "Port BIR - Kosongan 1") {
							$datakosongan += 1;
						}elseif ($datalastposition == "Port BIR - Kosongan 2") {
							$datakosongan += 1;
						}elseif ($datalastposition == "Simpang Bayah - Kosongan") {
							$datakosongan += 1;
						}elseif ($datalastposition == "KM 1" || $datalastposition == "KM 1.5" || $datalastposition == "KM 0.5") {
							$datakosongan += 1;
						}elseif ($datalastposition == "KM 2" || $datalastposition == "KM 2.5") {
							$datakosongan += 1;
						}elseif ($datalastposition == "KM 3" || $datalastposition == "KM 3.5") {
							$datakosongan += 1;
						}elseif ($datalastposition == "KM 4" || $datalastposition == "KM 4.5") {
							$datakosongan += 1;
						}elseif ($datalastposition == "KM 5" || $datalastposition == "KM 5.5") {
							$datakosongan += 1;
						}elseif ($datalastposition == "KM 6" || $datalastposition == "KM 6.5") {
							$datakosongan += 1;
						}elseif ($datalastposition == "KM 7" || $datalastposition == "KM 7.5") {
							$datakosongan += 1;
						}elseif ($datalastposition == "KM 8" || $datalastposition == "KM 8.5") {
							$datakosongan += 1;
						}elseif ($datalastposition == "KM 9" || $datalastposition == "KM 9.5") {
							$datakosongan += 1;
						}elseif ($datalastposition == "KM 10" || $datalastposition == "KM 10.5") {
							$datakosongan += 1;
						}elseif ($datalastposition == "KM 11" || $datalastposition == "KM 11.5") {
							$datakosongan += 1;
						}elseif ($datalastposition == "KM 12" || $datalastposition == "KM 12.5") {
							$datakosongan += 1;
						}elseif ($datalastposition == "KM 13" || $datalastposition == "KM 13.5") {
							$datakosongan += 1;
						}elseif ($datalastposition == "KM 14" || $datalastposition == "KM 14.5") {
							$datakosongan += 1;
						}elseif ($datalastposition == "KM 15" || $datalastposition == "KM 15.5") {
							$datakosongan += 1;
						}elseif ($datalastposition == "KM 16" || $datalastposition == "KM 16.5") {
							$datakosongan += 1;
						}elseif ($datalastposition == "KM 17" || $datalastposition == "KM 17.5") {
							$datakosongan += 1;
						}elseif ($datalastposition == "KM 18" || $datalastposition == "KM 18.5") {
							$datakosongan += 1;
						}elseif ($datalastposition == "KM 19" || $datalastposition == "KM 19.5") {
							$datakosongan += 1;
						}elseif ($datalastposition == "KM 20" || $datalastposition == "KM 20.5") {
							$datakosongan += 1;
						}elseif ($datalastposition == "KM 21" || $datalastposition == "KM 21.5") {
							$datakosongan += 1;
						}elseif ($datalastposition == "KM 22" || $datalastposition == "KM 22.5") {
							$datakosongan += 1;
						}elseif ($datalastposition == "KM 23" || $datalastposition == "KM 23.5") {
							$datakosongan += 1;
						}elseif ($datalastposition == "KM 24" || $datalastposition == "KM 24.5") {
							$datakosongan += 1;
						}elseif ($datalastposition == "KM 25" || $datalastposition == "KM 25.5") {
							$datakosongan += 1;
						}elseif ($datalastposition == "KM 26" || $datalastposition == "KM 26.5") {
							$datakosongan += 1;
						}elseif ($datalastposition == "KM 27" || $datalastposition == "KM 27.5") {
							$datakosongan += 1;
						}elseif ($datalastposition == "KM 28" || $datalastposition == "KM 28.5") {
							$datakosongan += 1;
						}elseif ($datalastposition == "KM 29" || $datalastposition == "KM 29.5") {
							$datakosongan += 1;
						}
					}
				}else {
					if ($jsonautocheck->auto_last_engine == "OFF") {
						if ($datalastposition == "Port BIB - Kosongan 1") {
							$datakosongan += 1;
						}elseif ($datalastposition == "Port BIB - Kosongan 2") {
							$datakosongan += 1;
						}elseif ($datalastposition == "Port BIR - Kosongan 1") {
							$datakosongan += 1;
						}elseif ($datalastposition == "Port BIR - Kosongan 2") {
							$datakosongan += 1;
						}elseif ($datalastposition == "Simpang Bayah - Kosongan") {
							$datakosongan += 1;
						}elseif ($datalastposition == "KM 1" || $datalastposition == "KM 1.5" || $datalastposition == "KM 0.5") {
							$datakosongan += 1;
						}elseif ($datalastposition == "KM 2" || $datalastposition == "KM 2.5") {
							$datakosongan += 1;
						}elseif ($datalastposition == "KM 3" || $datalastposition == "KM 3.5") {
							$datakosongan += 1;
						}elseif ($datalastposition == "KM 4" || $datalastposition == "KM 4.5") {
							$datakosongan += 1;
						}elseif ($datalastposition == "KM 5" || $datalastposition == "KM 5.5") {
							$datakosongan += 1;
						}elseif ($datalastposition == "KM 6" || $datalastposition == "KM 6.5") {
							$datakosongan += 1;
						}elseif ($datalastposition == "KM 7" || $datalastposition == "KM 7.5") {
							$datakosongan += 1;
						}elseif ($datalastposition == "KM 8" || $datalastposition == "KM 8.5") {
							$datakosongan += 1;
						}elseif ($datalastposition == "KM 9" || $datalastposition == "KM 9.5") {
							$datakosongan += 1;
						}elseif ($datalastposition == "KM 10" || $datalastposition == "KM 10.5") {
							$datakosongan += 1;
						}elseif ($datalastposition == "KM 11" || $datalastposition == "KM 11.5") {
							$datakosongan += 1;
						}elseif ($datalastposition == "KM 12" || $datalastposition == "KM 12.5") {
							$datakosongan += 1;
						}elseif ($datalastposition == "KM 13" || $datalastposition == "KM 13.5") {
							$datakosongan += 1;
						}elseif ($datalastposition == "KM 14" || $datalastposition == "KM 14.5") {
							$datakosongan += 1;
						}elseif ($datalastposition == "KM 15" || $datalastposition == "KM 15.5") {
							$datakosongan += 1;
						}elseif ($datalastposition == "KM 16" || $datalastposition == "KM 16.5") {
							$datakosongan += 1;
						}elseif ($datalastposition == "KM 17" || $datalastposition == "KM 17.5") {
							$datakosongan += 1;
						}elseif ($datalastposition == "KM 18" || $datalastposition == "KM 18.5") {
							$datakosongan += 1;
						}elseif ($datalastposition == "KM 19" || $datalastposition == "KM 19.5") {
							$datakosongan += 1;
						}elseif ($datalastposition == "KM 20" || $datalastposition == "KM 20.5") {
							$datakosongan += 1;
						}elseif ($datalastposition == "KM 21" || $datalastposition == "KM 21.5") {
							$datakosongan += 1;
						}elseif ($datalastposition == "KM 22" || $datalastposition == "KM 22.5") {
							$datakosongan += 1;
						}elseif ($datalastposition == "KM 23" || $datalastposition == "KM 23.5") {
							$datakosongan += 1;
						}elseif ($datalastposition == "KM 24" || $datalastposition == "KM 24.5") {
							$datakosongan += 1;
						}elseif ($datalastposition == "KM 25" || $datalastposition == "KM 25.5") {
							$datakosongan += 1;
						}elseif ($datalastposition == "KM 26" || $datalastposition == "KM 26.5") {
							$datakosongan += 1;
						}elseif ($datalastposition == "KM 27" || $datalastposition == "KM 27.5") {
							$datakosongan += 1;
						}elseif ($datalastposition == "KM 28" || $datalastposition == "KM 28.5") {
							$datakosongan += 1;
						}elseif ($datalastposition == "KM 29" || $datalastposition == "KM 29.5") {
							$datakosongan += 1;
						}
					}
				}
			}else if($jalur_name == "muatan") {
				if ($summary_mapsoption == 0) {
					if ($datalastposition == "Port BIB - Antrian") {
							$datamuatan += 1;
					}elseif ($datalastposition == "Port BIR - Antrian WB") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 1" || $datalastposition == "KM 1.5" || $datalastposition == "KM 0.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 2" || $datalastposition == "KM 2.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 3" || $datalastposition == "KM 3.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 4" || $datalastposition == "KM 4.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 5" || $datalastposition == "KM 5.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 6" || $datalastposition == "KM 6.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 7" || $datalastposition == "KM 7.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 8" || $datalastposition == "KM 8.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 9" || $datalastposition == "KM 9.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 10" || $datalastposition == "KM 10.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 11" || $datalastposition == "KM 11.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 12" || $datalastposition == "KM 12.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 13" || $datalastposition == "KM 13.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 14" || $datalastposition == "KM 14.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 15" || $datalastposition == "KM 15.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 16" || $datalastposition == "KM 16.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 17" || $datalastposition == "KM 17.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 18" || $datalastposition == "KM 18.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 19" || $datalastposition == "KM 19.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 20" || $datalastposition == "KM 20.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 21" || $datalastposition == "KM 21.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 22" || $datalastposition == "KM 22.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 23" || $datalastposition == "KM 23.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 24" || $datalastposition == "KM 24.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 25" || $datalastposition == "KM 25.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 26" || $datalastposition == "KM 26.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 27" || $datalastposition == "KM 27.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 28" || $datalastposition == "KM 28.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 29" || $datalastposition == "KM 29.5") {
							$datamuatan += 1;
					}
				}elseif($jsonautocheck->auto_last_engine == "ON") {
					if ($datalastposition == "Port BIB - Antrian") {
							$datamuatan += 1;
					}elseif ($datalastposition == "Port BIR - Antrian WB") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 1" || $datalastposition == "KM 1.5" || $datalastposition == "KM 0.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 2" || $datalastposition == "KM 2.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 3" || $datalastposition == "KM 3.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 4" || $datalastposition == "KM 4.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 5" || $datalastposition == "KM 5.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 6" || $datalastposition == "KM 6.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 7" || $datalastposition == "KM 7.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 8" || $datalastposition == "KM 8.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 9" || $datalastposition == "KM 9.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 10" || $datalastposition == "KM 10.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 11" || $datalastposition == "KM 11.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 12" || $datalastposition == "KM 12.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 13" || $datalastposition == "KM 13.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 14" || $datalastposition == "KM 14.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 15" || $datalastposition == "KM 15.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 16" || $datalastposition == "KM 16.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 17" || $datalastposition == "KM 17.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 18" || $datalastposition == "KM 18.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 19" || $datalastposition == "KM 19.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 20" || $datalastposition == "KM 20.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 21" || $datalastposition == "KM 21.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 22" || $datalastposition == "KM 22.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 23" || $datalastposition == "KM 23.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 24" || $datalastposition == "KM 24.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 25" || $datalastposition == "KM 25.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 26" || $datalastposition == "KM 26.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 27" || $datalastposition == "KM 27.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 28" || $datalastposition == "KM 28.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 29" || $datalastposition == "KM 29.5") {
							$datamuatan += 1;
					}
				}elseif($jsonautocheck->auto_last_engine == "OFF") {
					if ($datalastposition == "Port BIB - Antrian") {
							$datamuatan += 1;
					}elseif ($datalastposition == "Port BIR - Antrian WB") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 1" || $datalastposition == "KM 1.5" || $datalastposition == "KM 0.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 2" || $datalastposition == "KM 2.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 3" || $datalastposition == "KM 3.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 4" || $datalastposition == "KM 4.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 5" || $datalastposition == "KM 5.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 6" || $datalastposition == "KM 6.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 7" || $datalastposition == "KM 7.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 8" || $datalastposition == "KM 8.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 9" || $datalastposition == "KM 9.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 10" || $datalastposition == "KM 10.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 11" || $datalastposition == "KM 11.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 12" || $datalastposition == "KM 12.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 13" || $datalastposition == "KM 13.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 14" || $datalastposition == "KM 14.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 15" || $datalastposition == "KM 15.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 16" || $datalastposition == "KM 16.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 17" || $datalastposition == "KM 17.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 18" || $datalastposition == "KM 18.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 19" || $datalastposition == "KM 19.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 20" || $datalastposition == "KM 20.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 21" || $datalastposition == "KM 21.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 22" || $datalastposition == "KM 22.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 23" || $datalastposition == "KM 23.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 24" || $datalastposition == "KM 24.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 25" || $datalastposition == "KM 25.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 26" || $datalastposition == "KM 26.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 27" || $datalastposition == "KM 27.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 28" || $datalastposition == "KM 28.5") {
							$datamuatan += 1;
					}elseif ($datalastposition == "KM 29" || $datalastposition == "KM 29.5") {
							$datamuatan += 1;
					}
				}
			}

			// DATA OUT OF HAULING
			if ($datalastpositionoutofhauling == "out") {
				if ($summary_mapsoption == 0) {
					$dataoutofhauling += 1;
				}elseif($jsonautocheck->auto_last_engine == "ON") {
					$dataoutofhauling += 1;
				}elseif($jsonautocheck->auto_last_engine == "OFF") {
					$dataoutofhauling += 1;
				}
			}

			// DATA OFFLINE VEHICLE
			if ($auto_status == "M") {
				if ($summary_mapsoption == 0) {
					$dataofflinevehicle += 1;
				}elseif($jsonautocheck->auto_last_engine == "ON") {
					$dataofflinevehicle += 1;
				}elseif($jsonautocheck->auto_last_engine == "OFF") {
					$dataofflinevehicle += 1;
				}
			}

		if ($company_id != 0) {
			if ($company_id == $masterdatavehicle[$i]['vehicle_company']) {
				if ($summary_mapsoption == 0) {
						array_push($dataallunit, array(
							"engine_status" 				 => "all",
						));
				}elseif ($summary_mapsoption == 1) {
						if ($jsonautocheck->auto_last_engine == "ON") {
								array_push($dataallunit, array(
									"engine_status" 				 => $jsonautocheck->auto_last_engine,
								));
						}
					}else {
						if ($jsonautocheck->auto_last_engine == "OFF") {
								array_push($dataallunit, array(
									"engine_status" 				 => $jsonautocheck->auto_last_engine,
								));
						}
					}
			}
		}else {
			if ($summary_mapsoption == 0) {
					array_push($dataallunit, array(
						"engine_status" 				 => "all",
					));
			}elseif ($summary_mapsoption == 1) {
					if ($jsonautocheck->auto_last_engine == "ON") {
							array_push($dataallunit, array(
								"engine_status" 				 => $jsonautocheck->auto_last_engine,
							));
					}
				}else {
					if ($jsonautocheck->auto_last_engine == "OFF") {
							array_push($dataallunit, array(
								"engine_status" 				 => $jsonautocheck->auto_last_engine,
							));
					}
				}
		}
	}

	// echo "<pre>";
	// var_dump($dataallunit);die();
	// echo "<pre>";

	echo json_encode(
		array(
			"totalunit"           => sizeof($dataallunit),
			"totalinrom"          => $datainrom,
			"totalinport"         => $datainport,
			"totaloutofhauling"   => $dataoutofhauling,
			"totalofflinevehicle" => $dataofflinevehicle,
			"totalinpool" 				=> $datainpool,
			"totalkosongan"       => $datakosongan,
			"totalmuatan"         => $datamuatan,
		));
}

function dataconsolidated(){
	$dataMuatan   = $this->m_poipoolmaster->getmuatanperkm("ts_minidashboard", $this->sess->user_parent, $this->sess->user_id_role, $this->sess->user_id);
	$dataKosongan = $this->m_poipoolmaster->getkosonganperkm("ts_minidashboard", $this->sess->user_parent, $this->sess->user_id_role, $this->sess->user_id);

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
	$vehicleInRom = $this->m_poipoolmaster->getvehicleinrom("ts_minidashboard", $this->sess->user_parent, $this->sess->user_id_role, $this->sess->user_id);
		if ($vehicleInRom) {
			echo json_encode(array("msg" => "success", "code" => 200, "data" =>$vehicleInRom));
		}else {
			echo json_encode(array("msg" => "failed", "code" => 400));
		}
}

function vehicleonport(){
	$vehicleInPort = $this->m_poipoolmaster->getvehicleinport("ts_minidashboard", $this->sess->user_parent, $this->sess->user_id_role, $this->sess->user_id);
		if ($vehicleInPort) {
			echo json_encode(array("msg" => "success", "code" => 200, "data" =>$vehicleInPort));
		}else {
			echo json_encode(array("msg" => "failed", "code" => 400));
		}
}

function vehicleonpool(){
	$allCompany 					     = $this->m_poipoolmaster->getAllCompany();
	$dataVehicleOnPool         = array();
	$idpool 							     = $_POST['idpool'];
	$contractor 							 = $_POST['contractor'];
	$masterdatavehicle         = $this->m_poipoolmaster->getmastervehiclebycontractor($contractor);

	// echo "<pre>";
	// var_dump($idpool);die();
	// echo "<pre>";

		if ($idpool == "stli") {
			$street 					   = $this->dashboardmodel->getstreet_id("double", array("9309", "9401", "9402"));
		}else {
			$street 					   = $this->dashboardmodel->getstreet_id("", $idpool);
		}
	$streetFix 					     = explode(",", $street->street_name);
	$street_alias 					 = explode(",", $street->street_alias);

	$lasttimecheck          = json_decode($masterdatavehicle[0]['vehicle_autocheck']);

	$dataContractor['BKAE'] = 0;
	$dataContractor['KMB']  = 0;
	$dataContractor['GECL'] = 0;
	$dataContractor['BMT'] = 0;
	$dataContractor['RAMB'] = 0;
	$dataContractor['BBS']  = 0;
	$dataContractor['MKS']  = 0;
	$dataContractor['RBT']  = 0;
	$dataContractor['MMS']  = 0;
	$dataContractor['EST']  = 0;

	for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
		$autocheck          = json_decode($masterdatavehicle[$i]['vehicle_autocheck']);
		$auto_last_position = explode(",", $autocheck->auto_last_position);
		$datalastposition   = $auto_last_position[0];
		$auto_status   			= $autocheck->auto_status;

		// echo "<pre>";
		// var_dump($autocheck);die();
		// echo "<pre>";

		if ($datalastposition == $streetFix[0]) {
				if ($auto_status != "M" && $autocheck->auto_last_speed < 1) {
					array_push($dataVehicleOnPool, array(
						"vehicle_no"       => $masterdatavehicle[$i]['vehicle_no'],
						"vehicle_name"     => $masterdatavehicle[$i]['vehicle_name'],
						"vehicle_company"  => $masterdatavehicle[$i]['vehicle_company'],
						"auto_last_status" => $autocheck->auto_status,
						"auto_last_lat"    => $autocheck->auto_last_lat,
						"auto_last_long"   => $autocheck->auto_last_long,
						"auto_last_engine" => $autocheck->auto_last_engine,
						"auto_last_speed"  => $autocheck->auto_last_speed,
						"auto_last_update" => date("d-m-Y H:i:s", strtotime($autocheck->auto_last_update))
					));
				}
		}
	}

	for ($k=0; $k < sizeof($dataVehicleOnPool); $k++) {
		for ($j=0; $j < sizeof($allCompany); $j++) {
			if ($dataVehicleOnPool[$k]['vehicle_company'] == $allCompany[$j]['company_id']) {
				if ($allCompany[$j]['company_name'] == "BKAE") {
					$dataContractor['BKAE'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "KMB") {
					$dataContractor['KMB'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "GECL") {
					$dataContractor['GECL'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "BMT") {
					$dataContractor['BMT'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "RAMB") {
					$dataContractor['RAMB'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "BBS") {
					$dataContractor['BBS'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "MKS") {
					$dataContractor['MKS'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "RBT") {
					$dataContractor['RBT'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "MMS") {
					$dataContractor['MMS'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "EST") {
					$dataContractor['EST'] += 1;
				}
			}
		}
	}

	// echo "<pre>";
	// var_dump($dataContractor);die();
	// echo "<pre>";

	echo json_encode(array("msg" => "success", "code" => 200, "allCompany" => $allCompany, "jumlah_contractor" => $dataContractor, "data" => $dataVehicleOnPool, "statesent" => $street_alias, "lastcheck" => date("d-m-Y H:i:s", strtotime($lasttimecheck->auto_last_update))));
}

function showmapsafter(){
	$companyid 		 	   = $_POST['companyid'];
	$masterdatavehicle = $this->m_poipoolmaster->getmastervehiclebycontractorforheatmap($companyid);
	$poolmaster        = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");
	$datavehicle       = array();

		for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
			$autocheck         = json_decode($masterdatavehicle[$i]['vehicle_autocheck']);
					array_push($datavehicle, array(
						"vehicle_id"     => $masterdatavehicle[$i]['vehicle_id'],
						"vehicle_no"     => $masterdatavehicle[$i]['vehicle_no'],
						"vehicle_name"   => $masterdatavehicle[$i]['vehicle_name'],
						"vehicle_device" => $masterdatavehicle[$i]['vehicle_device'],
						"auto_last_lat"  => $autocheck->auto_last_lat,
						"auto_last_long" => $autocheck->auto_last_long
					));
		}

		// echo "<pre>";
		// var_dump($autocheck);die();
		// echo "<pre>";

		echo json_encode(array("msg" => "success", "code" => 200, "data" => $datavehicle, "poolmaster" => $poolmaster));
}

function outOfHauling_quickcount(){
	$companyid 		 	                  = $_POST['companyid'];
	$dataforclear                     = $this->m_poipoolmaster->getmastervehicle();
	$masterdatavehicle                = $this->m_poipoolmaster->getmastervehiclebycontractor($companyid);
	$poolmaster                       = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");
	$dataoutofhauling['outofhauling'] = 0;
	$dataVehicleOutofHauling          = array();
	$lasttimecheck 		                = date("d-m-Y H:i:s", strtotime("+1 hour"));

		for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
			$autocheck         = json_decode($masterdatavehicle[$i]['vehicle_autocheck']);
			$auto_last_hauling = explode(",", $autocheck->auto_last_hauling);
			$datalastposition  = $auto_last_hauling[0];

				if ($datalastposition == "out") {
					$dataoutofhauling['outofhauling'] += 1;

					array_push($dataVehicleOutofHauling, array(
						"vehicle_id"       => $masterdatavehicle[$i]['vehicle_id'],
						"vehicle_no"       => $masterdatavehicle[$i]['vehicle_no'],
						"vehicle_name"     => $masterdatavehicle[$i]['vehicle_name'],
						"vehicle_device"   => $masterdatavehicle[$i]['vehicle_device'],
						"auto_last_lat"    => $autocheck->auto_last_lat,
						"auto_last_long"   => $autocheck->auto_last_long,
						"auto_last_course" => $autocheck->auto_last_course,
					));
				}
		}

		// echo "<pre>";
		// var_dump($autocheck);die();
		// echo "<pre>";

		echo json_encode(array("msg" => "success", "code" => 200, "data" => $dataoutofhauling, "dataoutofhaulingmaps" => $dataVehicleOutofHauling, "poolmaster" => $poolmaster, "alldataforclearmaps" => $dataforclear, "lastcheck" => $lasttimecheck));
}

function getlistoutofhauling(){
	$allCompany 					  = $this->m_poipoolmaster->getAllCompany();
	$dataContractor['BKAE'] = 0;
	$dataContractor['KMB']  = 0;
	$dataContractor['GECL'] = 0;
	$dataContractor['STLI'] = 0;
	$dataContractor['RAMB'] = 0;
	$dataContractor['BBS']  = 0;
	$dataContractor['MKS']  = 0;
	$dataContractor['RBT']  = 0;
	$dataContractor['MMS']  = 0;
	$dataContractor['EST']  = 0;

	$contractor              = $_POST['contractor'];
	$masterdatavehicle       = $this->m_poipoolmaster->getmastervehiclebycontractor($contractor);
	$dataVehicleOutofHauling = array();

	for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
		$autocheck             = json_decode($masterdatavehicle[$i]['vehicle_autocheck']);
		$auto_last_hauling     = explode(",", $autocheck->auto_last_hauling);
		$datalastposition      = $auto_last_hauling[0];
		$auto_last_position    = explode(",", $autocheck->auto_last_position);
		$auto_last_positionfix = $auto_last_position[0];

		// echo "<pre>";
		// var_dump($auto_last_positionfix);die();
		// echo "<pre>";

			if ($datalastposition == "out") {
				array_push($dataVehicleOutofHauling, array(
					"vehicle_no"            => $masterdatavehicle[$i]['vehicle_no'],
					"vehicle_name"          => $masterdatavehicle[$i]['vehicle_name'],
					"vehicle_company"       => $masterdatavehicle[$i]['vehicle_company'],
					"vehicle_id_shareto"    => $masterdatavehicle[$i]['vehicle_id_shareto'],
					"auto_last_lat"         => $autocheck->auto_last_lat,
					"auto_last_long"        => $autocheck->auto_last_long,
					"auto_last_engine"      => $autocheck->auto_last_engine,
					"auto_last_speed"       => $autocheck->auto_last_speed,
					"auto_last_positionfix" => $auto_last_positionfix,
					"auto_last_update"      => date("d-m-Y H:i:s", strtotime($autocheck->auto_last_update))
				));
			}
	}

	for ($k=0; $k < sizeof($dataVehicleOutofHauling); $k++) {
		for ($j=0; $j < sizeof($allCompany); $j++) {
			if ($dataVehicleOutofHauling[$k]['vehicle_company'] == $allCompany[$j]['company_id']) {
				if ($allCompany[$j]['company_name'] == "BKAE") {
					$dataContractor['BKAE'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "KMB") {
					$dataContractor['KMB'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "GECL") {
					$dataContractor['GECL'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "STLI") {
					$dataContractor['STLI'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "RAMB") {
					$dataContractor['RAMB'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "BBS") {
					$dataContractor['BBS'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "MKS") {
					$dataContractor['MKS'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "RBT") {
					$dataContractor['RBT'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "MMS") {
					$dataContractor['MMS'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "EST") {
					$dataContractor['EST'] += 1;
				}
			}
		}
	}

	// echo "<pre>";
	// var_dump($dataContractor);die();
	// echo "<pre>";
	echo json_encode(array("msg" => "success", "code" => 200, "allCompany" => $allCompany, "jumlah_contractor" => $dataContractor, "data" => $dataVehicleOutofHauling));
}

function getlistoutofhaulingByContractor(){
	$contractor 						= $_POST['contractor'];
	$allCompany 					  = $this->m_poipoolmaster->getAllCompany();
	$dataContractor['BKAE'] = 0;
	$dataContractor['KMB']  = 0;
	$dataContractor['GECL'] = 0;
	$dataContractor['STLI'] = 0;
	$dataContractor['RAMB'] = 0;
	$dataContractor['BBS']  = 0;
	$dataContractor['MKS']  = 0;
	$dataContractor['RBT']  = 0;
	$dataContractor['MMS']  = 0;
	$dataContractor['EST']  = 0;

	$masterdatavehicle       = $this->m_poipoolmaster->getmastervehiclebycontractor($contractor);
	$dataVehicleOutofHauling = array();

	for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
		$autocheck             = json_decode($masterdatavehicle[$i]['vehicle_autocheck']);
		$auto_last_hauling     = explode(",", $autocheck->auto_last_hauling);
		$datalastposition      = $auto_last_hauling[0];
		$auto_last_position    = explode(",", $autocheck->auto_last_position);
		$auto_last_positionfix = $auto_last_position[0];

		// echo "<pre>";
		// var_dump($auto_last_positionfix);die();
		// echo "<pre>";

			if ($datalastposition == "out") {
				array_push($dataVehicleOutofHauling, array(
					"vehicle_no"            => $masterdatavehicle[$i]['vehicle_no'],
					"vehicle_name"          => $masterdatavehicle[$i]['vehicle_name'],
					"vehicle_company"       => $masterdatavehicle[$i]['vehicle_company'],
					"auto_last_lat"         => $autocheck->auto_last_lat,
					"auto_last_long"        => $autocheck->auto_last_long,
					"auto_last_engine"      => $autocheck->auto_last_engine,
					"auto_last_speed"       => $autocheck->auto_last_speed,
					"auto_last_positionfix" => $auto_last_positionfix,
					"auto_last_update"      => date("d-m-Y H:i:s", strtotime($autocheck->auto_last_update))
				));
			}
	}

	for ($k=0; $k < sizeof($dataVehicleOutofHauling); $k++) {
		for ($j=0; $j < sizeof($allCompany); $j++) {
			if ($dataVehicleOutofHauling[$k]['vehicle_company'] == $allCompany[$j]['company_id']) {
				if ($allCompany[$j]['company_name'] == "BKAE") {
					$dataContractor['BKAE'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "KMB") {
					$dataContractor['KMB'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "GECL") {
					$dataContractor['GECL'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "STLI") {
					$dataContractor['STLI'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "RAMB") {
					$dataContractor['RAMB'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "BBS") {
					$dataContractor['BBS'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "MKS") {
					$dataContractor['MKS'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "RBT") {
					$dataContractor['RBT'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "MMS") {
					$dataContractor['MMS'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "EST") {
					$dataContractor['EST'] += 1;
				}
			}
		}
	}

	// echo "<pre>";
	// var_dump($dataContractor);die();
	// echo "<pre>";
	echo json_encode(array("msg" => "success", "code" => 200, "allCompany" => $allCompany, "jumlah_contractor" => $dataContractor, "data" => $dataVehicleOutofHauling));
}

function offlinevehicle_quickcount(){
	$allCompany 					  = $this->m_poipoolmaster->getAllCompany();
	$dataContractor['BKAE'] = 0;
	$dataContractor['KMB']  = 0;
	$dataContractor['GECL'] = 0;
	$dataContractor['STLI'] = 0;
	$dataContractor['RAMB'] = 0;
	$dataContractor['BBS']  = 0;
	$dataContractor['MKS']  = 0;
	$dataContractor['RBT']  = 0;
	$dataContractor['MMS']  = 0;
	$dataContractor['EST']  = 0;
	$lasttimecheck 		      = date("d-m-Y H:i:s", strtotime("+1 hour"));

	$contractor        = $_POST['contractor'];
	$masterdatavehicle = $this->m_poipoolmaster->getmasterofflinevehicle($contractor);

	// echo "<pre>";
	// var_dump($masterdatavehicle);die();
	// echo "<pre>";

	$datavehicleoffline['offlinevehicle'] = 0;
	$offlinevehicle                       = array();

	for($i=0; $i < count($masterdatavehicle); $i++){
		$json                  = json_decode($masterdatavehicle[$i]['vehicle_autocheck']);
		$auto_last_position    = explode(",", $json->auto_last_position);
		$auto_last_positionfix = $auto_last_position[0];

		if(isset($json)){
			if($json->auto_status == "M" ){
				array_push($offlinevehicle, array(
					"vehicle_no"            => $masterdatavehicle[$i]['vehicle_no'],
					"vehicle_name"          => $masterdatavehicle[$i]['vehicle_name'],
					"vehicle_company"       => $masterdatavehicle[$i]['vehicle_company'],
					"auto_last_lat"         => $json->auto_last_lat,
					"auto_last_long"        => $json->auto_last_long,
					"auto_last_engine"      => $json->auto_last_engine,
					"auto_last_speed"       => $json->auto_last_speed,
					"auto_last_positionfix" => $auto_last_positionfix,
					"auto_last_update"      => date("d-m-Y H:i:s", strtotime($json->auto_last_update)),
					"status"                => "Status M"
				));
			}
		}
	}

	for ($k=0; $k < sizeof($offlinevehicle); $k++) {
		for ($j=0; $j < sizeof($allCompany); $j++) {
			if ($offlinevehicle[$k]['vehicle_company'] == $allCompany[$j]['company_id']) {
				if ($allCompany[$j]['company_name'] == "BKAE") {
					$dataContractor['BKAE'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "KMB") {
					$dataContractor['KMB'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "GECL") {
					$dataContractor['GECL'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "STLI") {
					$dataContractor['STLI'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "RAMB") {
					$dataContractor['RAMB'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "BBS") {
					$dataContractor['BBS'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "MKS") {
					$dataContractor['MKS'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "RBT") {
					$dataContractor['RBT'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "MMS") {
					$dataContractor['MMS'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "EST") {
					$dataContractor['EST'] += 1;
				}
			}
		}
	}

		// echo "<pre>";
		// var_dump($offlinevehicle);die();
		// echo "<pre>";
		echo json_encode(array("msg" => "success", "code" => 200, "allCompany" => $allCompany, "jumlah_contractor" => $dataContractor, "data" => $offlinevehicle, "lastcheck" => $lasttimecheck));
}

function km_quickcount(){
	$masterdatavehicle = $this->m_poipoolmaster->getmastervehicle();
	$dataKmMuatanFix   = array();
	$dataKmKosonganFix = array();

	$dataJumlahInKmMuatan['KM_0']  = 0;
	$dataJumlahInKmMuatan['KM_1']  = 0;
	$dataJumlahInKmMuatan['KM_2']  = 0;
	$dataJumlahInKmMuatan['KM_3']  = 0;
	$dataJumlahInKmMuatan['KM_4']  = 0;
	$dataJumlahInKmMuatan['KM_5']  = 0;
	$dataJumlahInKmMuatan['KM_6']  = 0;
	$dataJumlahInKmMuatan['KM_7']  = 0;
	$dataJumlahInKmMuatan['KM_8']  = 0;
	$dataJumlahInKmMuatan['KM_9']  = 0;
	$dataJumlahInKmMuatan['KM_10'] = 0;
	$dataJumlahInKmMuatan['KM_11'] = 0;
	$dataJumlahInKmMuatan['KM_12'] = 0;
	$dataJumlahInKmMuatan['KM_13'] = 0;
	$dataJumlahInKmMuatan['KM_14'] = 0;
	$dataJumlahInKmMuatan['KM_15'] = 0;
	$dataJumlahInKmMuatan['KM_16'] = 0;
	$dataJumlahInKmMuatan['KM_17'] = 0;
	$dataJumlahInKmMuatan['KM_18'] = 0;
	$dataJumlahInKmMuatan['KM_19'] = 0;
	$dataJumlahInKmMuatan['KM_20'] = 0;
	$dataJumlahInKmMuatan['KM_21'] = 0;
	$dataJumlahInKmMuatan['KM_22'] = 0;
	$dataJumlahInKmMuatan['KM_23'] = 0;
	$dataJumlahInKmMuatan['KM_24'] = 0;
	$dataJumlahInKmMuatan['KM_25'] = 0;
	$dataJumlahInKmMuatan['KM_26'] = 0;
	$dataJumlahInKmMuatan['KM_27'] = 0;
	$dataJumlahInKmMuatan['KM_28'] = 0;
	$dataJumlahInKmMuatan['KM_29'] = 0;
	$dataJumlahInKmMuatan['KM_30'] = 0;

	$dataJumlahInKmKosongan['KM_0']  = 0;
	$dataJumlahInKmKosongan['KM_1']  = 0;
	$dataJumlahInKmKosongan['KM_2']  = 0;
	$dataJumlahInKmKosongan['KM_3']  = 0;
	$dataJumlahInKmKosongan['KM_4']  = 0;
	$dataJumlahInKmKosongan['KM_5']  = 0;
	$dataJumlahInKmKosongan['KM_6']  = 0;
	$dataJumlahInKmKosongan['KM_7']  = 0;
	$dataJumlahInKmKosongan['KM_8']  = 0;
	$dataJumlahInKmKosongan['KM_9']  = 0;
	$dataJumlahInKmKosongan['KM_10'] = 0;
	$dataJumlahInKmKosongan['KM_11'] = 0;
	$dataJumlahInKmKosongan['KM_12'] = 0;
	$dataJumlahInKmKosongan['KM_13'] = 0;
	$dataJumlahInKmKosongan['KM_14'] = 0;
	$dataJumlahInKmKosongan['KM_15'] = 0;
	$dataJumlahInKmKosongan['KM_16'] = 0;
	$dataJumlahInKmKosongan['KM_17'] = 0;
	$dataJumlahInKmKosongan['KM_18'] = 0;
	$dataJumlahInKmKosongan['KM_19'] = 0;
	$dataJumlahInKmKosongan['KM_20'] = 0;
	$dataJumlahInKmKosongan['KM_21'] = 0;
	$dataJumlahInKmKosongan['KM_22'] = 0;
	$dataJumlahInKmKosongan['KM_23'] = 0;
	$dataJumlahInKmKosongan['KM_24'] = 0;
	$dataJumlahInKmKosongan['KM_25'] = 0;
	$dataJumlahInKmKosongan['KM_26'] = 0;
	$dataJumlahInKmKosongan['KM_27'] = 0;
	$dataJumlahInKmKosongan['KM_28'] = 0;
	$dataJumlahInKmKosongan['KM_29'] = 0;
	$dataJumlahInKmKosongan['KM_30'] = 0;

	$lasttimecheck = date("d-m-Y H:i:s", strtotime("+1 hour"));

	for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
		$autocheck          = json_decode($masterdatavehicle[$i]['vehicle_autocheck']);
		$auto_last_position = explode(",", $autocheck->auto_last_position);
		$jalur_name         = $autocheck->auto_last_road;
		$datalastposition   = $auto_last_position[0];

			if ($jalur_name == "kosongan") {
				if ($datalastposition == "KM 0" || $datalastposition == "KM 0.5") {
					$dataJumlahInKmKosongan['KM_1'] += 1;
				}elseif ($datalastposition == "KM 1" || $datalastposition == "KM 1.5") {
					$dataJumlahInKmKosongan['KM_2'] += 1;
				}elseif ($datalastposition == "KM 2" || $datalastposition == "KM 2.5") {
					$dataJumlahInKmKosongan['KM_3'] += 1;
				}elseif ($datalastposition == "KM 3" || $datalastposition == "KM 3.5") {
					$dataJumlahInKmKosongan['KM_4'] += 1;
				}elseif ($datalastposition == "KM 4" || $datalastposition == "KM 4.5") {
					$dataJumlahInKmKosongan['KM_5'] += 1;
				}elseif ($datalastposition == "KM 5" || $datalastposition == "KM 5.5") {
					$dataJumlahInKmKosongan['KM_6'] += 1;
				}elseif ($datalastposition == "KM 6" || $datalastposition == "KM 6.5") {
					$dataJumlahInKmKosongan['KM_7'] += 1;
				}elseif ($datalastposition == "KM 7" || $datalastposition == "KM 7.5") {
					$dataJumlahInKmKosongan['KM_8'] += 1;
				}elseif ($datalastposition == "KM 8" || $datalastposition == "KM 8.5") {
					$dataJumlahInKmKosongan['KM_9'] += 1;
				}elseif ($datalastposition == "KM 9" || $datalastposition == "KM 9.5") {
					$dataJumlahInKmKosongan['KM_10'] += 1;
				}elseif ($datalastposition == "KM 10" || $datalastposition == "KM 10.5") {
					$dataJumlahInKmKosongan['KM_11'] += 1;
				}elseif ($datalastposition == "KM 11" || $datalastposition == "KM 11.5") {
					$dataJumlahInKmKosongan['KM_12'] += 1;
				}elseif ($datalastposition == "KM 12" || $datalastposition == "KM 12.5") {
					$dataJumlahInKmKosongan['KM_13'] += 1;
				}elseif ($datalastposition == "KM 13" || $datalastposition == "KM 13.5") {
					$dataJumlahInKmKosongan['KM_14'] += 1;
				}elseif ($datalastposition == "KM 14" || $datalastposition == "KM 14.5") {
					$dataJumlahInKmKosongan['KM_15'] += 1;
				}elseif ($datalastposition == "KM 15" || $datalastposition == "KM 15.5") {
					$dataJumlahInKmKosongan['KM_16'] += 1;
				}elseif ($datalastposition == "KM 16" || $datalastposition == "KM 16.5") {
					$dataJumlahInKmKosongan['KM_17'] += 1;
				}elseif ($datalastposition == "KM 17" || $datalastposition == "KM 17.5") {
					$dataJumlahInKmKosongan['KM_18'] += 1;
				}elseif ($datalastposition == "KM 18" || $datalastposition == "KM 18.5") {
					$dataJumlahInKmKosongan['KM_19'] += 1;
				}elseif ($datalastposition == "KM 19" || $datalastposition == "KM 19.5") {
					$dataJumlahInKmKosongan['KM_20'] += 1;
				}elseif ($datalastposition == "KM 20" || $datalastposition == "KM 20.5") {
					$dataJumlahInKmKosongan['KM_21'] += 1;
				}elseif ($datalastposition == "KM 21" || $datalastposition == "KM 21.5") {
					$dataJumlahInKmKosongan['KM_22'] += 1;
				}elseif ($datalastposition == "KM 22" || $datalastposition == "KM 22.5") {
					$dataJumlahInKmKosongan['KM_23'] += 1;
				}elseif ($datalastposition == "KM 23" || $datalastposition == "KM 23.5") {
					$dataJumlahInKmKosongan['KM_24'] += 1;
				}elseif ($datalastposition == "KM 24" || $datalastposition == "KM 24.5") {
					$dataJumlahInKmKosongan['KM_25'] += 1;
				}elseif ($datalastposition == "KM 25" || $datalastposition == "KM 25.5") {
					$dataJumlahInKmKosongan['KM_26'] += 1;
				}elseif ($datalastposition == "KM 26" || $datalastposition == "KM 26.5") {
					$dataJumlahInKmKosongan['KM_27'] += 1;
				}elseif ($datalastposition == "KM 27" || $datalastposition == "KM 27.5") {
					$dataJumlahInKmKosongan['KM_28'] += 1;
				}elseif ($datalastposition == "KM 28" || $datalastposition == "KM 28.5") {
					$dataJumlahInKmKosongan['KM_29'] += 1;
				}elseif ($datalastposition == "KM 29" || $datalastposition == "KM 29.5") {
					$dataJumlahInKmKosongan['KM_30'] += 1;
				}
			}else {
				if ($datalastposition == "KM 0" || $datalastposition == "KM 0.5") {
					$dataJumlahInKmMuatan['KM_1'] += 1;
				}elseif ($datalastposition == "KM 1" || $datalastposition == "KM 1.5") {
					$dataJumlahInKmMuatan['KM_2'] += 1;
				}elseif ($datalastposition == "KM 2" || $datalastposition == "KM 2.5") {
					$dataJumlahInKmMuatan['KM_3'] += 1;
				}elseif ($datalastposition == "KM 3" || $datalastposition == "KM 3.5") {
					$dataJumlahInKmMuatan['KM_4'] += 1;
				}elseif ($datalastposition == "KM 4" || $datalastposition == "KM 4.5") {
					$dataJumlahInKmMuatan['KM_5'] += 1;
				}elseif ($datalastposition == "KM 5" || $datalastposition == "KM 5.5") {
					$dataJumlahInKmMuatan['KM_6'] += 1;
				}elseif ($datalastposition == "KM 6" || $datalastposition == "KM 6.5") {
					$dataJumlahInKmMuatan['KM_7'] += 1;
				}elseif ($datalastposition == "KM 7" || $datalastposition == "KM 7.5") {
					$dataJumlahInKmMuatan['KM_8'] += 1;
				}elseif ($datalastposition == "KM 8" || $datalastposition == "KM 8.5") {
					$dataJumlahInKmMuatan['KM_9'] += 1;
				}elseif ($datalastposition == "KM 9" || $datalastposition == "KM 9.5") {
					$dataJumlahInKmMuatan['KM_10'] += 1;
				}elseif ($datalastposition == "KM 10" || $datalastposition == "KM 10.5") {
					$dataJumlahInKmMuatan['KM_11'] += 1;
				}elseif ($datalastposition == "KM 11" || $datalastposition == "KM 11.5") {
					$dataJumlahInKmMuatan['KM_12'] += 1;
				}elseif ($datalastposition == "KM 12" || $datalastposition == "KM 12.5") {
					$dataJumlahInKmMuatan['KM_13'] += 1;
				}elseif ($datalastposition == "KM 13" || $datalastposition == "KM 13.5") {
					$dataJumlahInKmMuatan['KM_14'] += 1;
				}elseif ($datalastposition == "KM 14" || $datalastposition == "KM 14.5") {
					$dataJumlahInKmMuatan['KM_15'] += 1;
				}elseif ($datalastposition == "KM 15" || $datalastposition == "KM 15.5") {
					$dataJumlahInKmMuatan['KM_16'] += 1;
				}elseif ($datalastposition == "KM 16" || $datalastposition == "KM 16.5") {
					$dataJumlahInKmMuatan['KM_17'] += 1;
				}elseif ($datalastposition == "KM 17" || $datalastposition == "KM 17.5") {
					$dataJumlahInKmMuatan['KM_18'] += 1;
				}elseif ($datalastposition == "KM 18" || $datalastposition == "KM 18.5") {
					$dataJumlahInKmMuatan['KM_19'] += 1;
				}elseif ($datalastposition == "KM 19" || $datalastposition == "KM 19.5") {
					$dataJumlahInKmMuatan['KM_20'] += 1;
				}elseif ($datalastposition == "KM 20" || $datalastposition == "KM 20.5") {
					$dataJumlahInKmMuatan['KM_21'] += 1;
				}elseif ($datalastposition == "KM 21" || $datalastposition == "KM 21.5") {
					$dataJumlahInKmMuatan['KM_22'] += 1;
				}elseif ($datalastposition == "KM 22" || $datalastposition == "KM 22.5") {
					$dataJumlahInKmMuatan['KM_23'] += 1;
				}elseif ($datalastposition == "KM 23" || $datalastposition == "KM 23.5") {
					$dataJumlahInKmMuatan['KM_24'] += 1;
				}elseif ($datalastposition == "KM 24" || $datalastposition == "KM 24.5") {
					$dataJumlahInKmMuatan['KM_25'] += 1;
				}elseif ($datalastposition == "KM 25" || $datalastposition == "KM 25.5") {
					$dataJumlahInKmMuatan['KM_26'] += 1;
				}elseif ($datalastposition == "KM 26" || $datalastposition == "KM 26.5") {
					$dataJumlahInKmMuatan['KM_27'] += 1;
				}elseif ($datalastposition == "KM 27" || $datalastposition == "KM 27.5") {
					$dataJumlahInKmMuatan['KM_28'] += 1;
				}elseif ($datalastposition == "KM 28" || $datalastposition == "KM 28.5") {
					$dataJumlahInKmMuatan['KM_29'] += 1;
				}elseif ($datalastposition == "KM 29" || $datalastposition == "KM 29.5") {
					$dataJumlahInKmMuatan['KM_30'] += 1;
				}
			}
	}

	// LIMIT SETTING PER KM
	$getdataFromStreet = $this->m_poipoolmaster->getstreet_now(1);
	$mapSettingType    = $this->m_poipoolmaster->getMapSettingByType(1);

	$postfix_middle_limit = "_middle_limit";
	$postfix_top_limit    = "_top_limit";

	if (isset($getdataFromStreet)) {
		$datafixlimitperkm = array();
		for ($i=0; $i < sizeof($getdataFromStreet); $i++) {
			$streetremovecoma         = explode(",", $getdataFromStreet[$i]['street_name']);
			$streetfix                = str_replace(" ", "_", $streetremovecoma[0]);

			$middlelimitname          = $streetfix.$postfix_middle_limit;
			$toplimitname             = $streetfix.$postfix_top_limit;

			$getMapSettingByLimitName = $this->m_poipoolmaster->getThisMapSettingByLimitName($middlelimitname, $toplimitname);

			if (sizeof($getMapSettingByLimitName) > 1) {
					array_push($datafixlimitperkm, array(
						"street_id"               => $getdataFromStreet[$i]['street_id'],
						"street_name"             => $getdataFromStreet[$i]['street_name'],
						"mapsetting_type"         => 1,
						"mapsetting_name_alias"   => $streetremovecoma[0],
						"mapsetting_name"         => $streetfix,
						"mapsetting_middle_limit" => $getMapSettingByLimitName[0]['mapsetting_limit_value'],
						"mapsetting_top_limit"    => $getMapSettingByLimitName[1]['mapsetting_limit_value']
					));
			}else {
				array_push($datafixlimitperkm, array(
					"street_id"               => $getdataFromStreet[$i]['street_id'],
					"street_name"             => $getdataFromStreet[$i]['street_name'],
					"mapsetting_type"         => 1,
					"mapsetting_name_alias"   => $streetremovecoma[0],
					"mapsetting_name"         => $streetfix,
					"mapsetting_middle_limit" => 0,
					"mapsetting_top_limit"    => 0
				));
			}
		}
	}

	// echo "<pre>";
	// var_dump($datafixlimitperkm);die();
	// echo "<pre>";
	// LIMIT SETTING PER KM

	// echo "<pre>";
	// var_dump($dataJumlahInKmMuatan);die();
	// echo "<pre>";

	echo json_encode(array("msg" => "success", "code" => 200, "dataMuatan" => $dataJumlahInKmMuatan, "dataKosongan" => $dataJumlahInKmKosongan, "lastcheck" => $lasttimecheck, "datafixlimitperkm" => $datafixlimitperkm));
}

function km_quickcount_new(){
	$companyid 				 = $_POST['companyid'];
	$masterdatavehicle = $this->m_poipoolmaster->getmastervehiclebycontractor($companyid);
	$dataKmMuatanFix   = array();
	$dataKmKosonganFix = array();

	// echo "<pre>";
	// var_dump($masterdatavehicle);die();
	// echo "<pre>";

	$dataJumlahInKmKosongan_2['gb0_port_bib_kosongan_1']    = 0;
	$dataJumlahInKmKosongan_2['gb1_port_bib_kosongan_2']    = 0;
	$dataJumlahInKmKosongan_2['gb2_port_bir_kosongan_1']    = 0;
	$dataJumlahInKmKosongan_2['gb3_port_bir_kosongan_2']    = 0;
	$dataJumlahInKmKosongan_2['gb4_simpang_bayah_kosongan'] = 0;

	$dataJumlahInKmMuatan_2['gb5_port_bib_antrian']         = 0;
	$dataJumlahInKmMuatan_2['gb6_port_bir_antrian_wb']      = 0;

	$dataJumlahInKmMuatan['KM_0']  = 0;
	$dataJumlahInKmMuatan['KM_1']  = 0;
	$dataJumlahInKmMuatan['KM_2']  = 0;
	$dataJumlahInKmMuatan['KM_3']  = 0;
	$dataJumlahInKmMuatan['KM_4']  = 0;
	$dataJumlahInKmMuatan['KM_5']  = 0;
	$dataJumlahInKmMuatan['KM_6']  = 0;
	$dataJumlahInKmMuatan['KM_7']  = 0;
	$dataJumlahInKmMuatan['KM_8']  = 0;
	$dataJumlahInKmMuatan['KM_9']  = 0;
	$dataJumlahInKmMuatan['KM_10'] = 0;
	$dataJumlahInKmMuatan['KM_11'] = 0;
	$dataJumlahInKmMuatan['KM_12'] = 0;
	$dataJumlahInKmMuatan['KM_13'] = 0;
	$dataJumlahInKmMuatan['KM_14'] = 0;
	$dataJumlahInKmMuatan['KM_15'] = 0;
	$dataJumlahInKmMuatan['KM_16'] = 0;
	$dataJumlahInKmMuatan['KM_17'] = 0;
	$dataJumlahInKmMuatan['KM_18'] = 0;
	$dataJumlahInKmMuatan['KM_19'] = 0;
	$dataJumlahInKmMuatan['KM_20'] = 0;
	$dataJumlahInKmMuatan['KM_21'] = 0;
	$dataJumlahInKmMuatan['KM_22'] = 0;
	$dataJumlahInKmMuatan['KM_23'] = 0;
	$dataJumlahInKmMuatan['KM_24'] = 0;
	$dataJumlahInKmMuatan['KM_25'] = 0;
	$dataJumlahInKmMuatan['KM_26'] = 0;
	$dataJumlahInKmMuatan['KM_27'] = 0;
	$dataJumlahInKmMuatan['KM_28'] = 0;
	$dataJumlahInKmMuatan['KM_29'] = 0;
	$dataJumlahInKmMuatan['KM_30'] = 0;

	$dataJumlahInKmKosongan['KM_0']  = 0;
	$dataJumlahInKmKosongan['KM_1']  = 0;
	$dataJumlahInKmKosongan['KM_2']  = 0;
	$dataJumlahInKmKosongan['KM_3']  = 0;
	$dataJumlahInKmKosongan['KM_4']  = 0;
	$dataJumlahInKmKosongan['KM_5']  = 0;
	$dataJumlahInKmKosongan['KM_6']  = 0;
	$dataJumlahInKmKosongan['KM_7']  = 0;
	$dataJumlahInKmKosongan['KM_8']  = 0;
	$dataJumlahInKmKosongan['KM_9']  = 0;
	$dataJumlahInKmKosongan['KM_10'] = 0;
	$dataJumlahInKmKosongan['KM_11'] = 0;
	$dataJumlahInKmKosongan['KM_12'] = 0;
	$dataJumlahInKmKosongan['KM_13'] = 0;
	$dataJumlahInKmKosongan['KM_14'] = 0;
	$dataJumlahInKmKosongan['KM_15'] = 0;
	$dataJumlahInKmKosongan['KM_16'] = 0;
	$dataJumlahInKmKosongan['KM_17'] = 0;
	$dataJumlahInKmKosongan['KM_18'] = 0;
	$dataJumlahInKmKosongan['KM_19'] = 0;
	$dataJumlahInKmKosongan['KM_20'] = 0;
	$dataJumlahInKmKosongan['KM_21'] = 0;
	$dataJumlahInKmKosongan['KM_22'] = 0;
	$dataJumlahInKmKosongan['KM_23'] = 0;
	$dataJumlahInKmKosongan['KM_24'] = 0;
	$dataJumlahInKmKosongan['KM_25'] = 0;
	$dataJumlahInKmKosongan['KM_26'] = 0;
	$dataJumlahInKmKosongan['KM_27'] = 0;
	$dataJumlahInKmKosongan['KM_28'] = 0;
	$dataJumlahInKmKosongan['KM_29'] = 0;
	$dataJumlahInKmKosongan['KM_30'] = 0;

	$lasttimecheck = date("d-m-Y H:i:s", strtotime("+1 hour"));

	for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
		$autocheck          = json_decode($masterdatavehicle[$i]['vehicle_autocheck']);
		$auto_last_position = explode(",", $autocheck->auto_last_position);
		$jalur_name         = $autocheck->auto_last_road;
		$datalastposition   = $auto_last_position[0];
		$auto_status 				= $autocheck->auto_status;

		// echo "<pre>";
		// var_dump($auto_status);die();
		// echo "<pre>";

			if ($auto_status != "M") {
			if ($jalur_name == "kosongan") {
				if ($datalastposition == "Port BIB - Kosongan 1") {
					$dataJumlahInKmKosongan_2['gb0_port_bib_kosongan_1'] += 1;
				}elseif ($datalastposition == "Port BIB - Kosongan 2") {
					$dataJumlahInKmKosongan_2['gb1_port_bib_kosongan_2'] += 1;
				}elseif ($datalastposition == "Port BIR - Kosongan 1") {
					$dataJumlahInKmKosongan_2['gb2_port_bir_kosongan_1'] += 1;
				}elseif ($datalastposition == "Port BIR - Kosongan 2") {
					$dataJumlahInKmKosongan_2['gb3_port_bir_kosongan_2'] += 1;
				}elseif ($datalastposition == "Simpang Bayah - Kosongan") {
					$dataJumlahInKmKosongan_2['gb4_simpang_bayah_kosongan'] += 1;
				}elseif ($datalastposition == "KM 1" || $datalastposition == "KM 1.5" || $datalastposition == "KM 0.5") {
					$dataJumlahInKmKosongan['KM_2'] += 1;
				}elseif ($datalastposition == "KM 2" || $datalastposition == "KM 2.5") {
					$dataJumlahInKmKosongan['KM_3'] += 1;
				}elseif ($datalastposition == "KM 3" || $datalastposition == "KM 3.5") {
					$dataJumlahInKmKosongan['KM_4'] += 1;
				}elseif ($datalastposition == "KM 4" || $datalastposition == "KM 4.5") {
					$dataJumlahInKmKosongan['KM_5'] += 1;
				}elseif ($datalastposition == "KM 5" || $datalastposition == "KM 5.5") {
					$dataJumlahInKmKosongan['KM_6'] += 1;
				}elseif ($datalastposition == "KM 6" || $datalastposition == "KM 6.5") {
					$dataJumlahInKmKosongan['KM_7'] += 1;
				}elseif ($datalastposition == "KM 7" || $datalastposition == "KM 7.5") {
					$dataJumlahInKmKosongan['KM_8'] += 1;
				}elseif ($datalastposition == "KM 8" || $datalastposition == "KM 8.5") {
					$dataJumlahInKmKosongan['KM_9'] += 1;
				}elseif ($datalastposition == "KM 9" || $datalastposition == "KM 9.5") {
					$dataJumlahInKmKosongan['KM_10'] += 1;
				}elseif ($datalastposition == "KM 10" || $datalastposition == "KM 10.5") {
					$dataJumlahInKmKosongan['KM_11'] += 1;
				}elseif ($datalastposition == "KM 11" || $datalastposition == "KM 11.5") {
					$dataJumlahInKmKosongan['KM_12'] += 1;
				}elseif ($datalastposition == "KM 12" || $datalastposition == "KM 12.5") {
					$dataJumlahInKmKosongan['KM_13'] += 1;
				}elseif ($datalastposition == "KM 13" || $datalastposition == "KM 13.5") {
					$dataJumlahInKmKosongan['KM_14'] += 1;
				}elseif ($datalastposition == "KM 14" || $datalastposition == "KM 14.5") {
					$dataJumlahInKmKosongan['KM_15'] += 1;
				}elseif ($datalastposition == "KM 15" || $datalastposition == "KM 15.5") {
					$dataJumlahInKmKosongan['KM_16'] += 1;
				}elseif ($datalastposition == "KM 16" || $datalastposition == "KM 16.5") {
					$dataJumlahInKmKosongan['KM_17'] += 1;
				}elseif ($datalastposition == "KM 17" || $datalastposition == "KM 17.5") {
					$dataJumlahInKmKosongan['KM_18'] += 1;
				}elseif ($datalastposition == "KM 18" || $datalastposition == "KM 18.5") {
					$dataJumlahInKmKosongan['KM_19'] += 1;
				}elseif ($datalastposition == "KM 19" || $datalastposition == "KM 19.5") {
					$dataJumlahInKmKosongan['KM_20'] += 1;
				}elseif ($datalastposition == "KM 20" || $datalastposition == "KM 20.5") {
					$dataJumlahInKmKosongan['KM_21'] += 1;
				}elseif ($datalastposition == "KM 21" || $datalastposition == "KM 21.5") {
					$dataJumlahInKmKosongan['KM_22'] += 1;
				}elseif ($datalastposition == "KM 22" || $datalastposition == "KM 22.5") {
					$dataJumlahInKmKosongan['KM_23'] += 1;
				}elseif ($datalastposition == "KM 23" || $datalastposition == "KM 23.5") {
					$dataJumlahInKmKosongan['KM_24'] += 1;
				}elseif ($datalastposition == "KM 24" || $datalastposition == "KM 24.5") {
					$dataJumlahInKmKosongan['KM_25'] += 1;
				}elseif ($datalastposition == "KM 25" || $datalastposition == "KM 25.5") {
					$dataJumlahInKmKosongan['KM_26'] += 1;
				}elseif ($datalastposition == "KM 26" || $datalastposition == "KM 26.5") {
					$dataJumlahInKmKosongan['KM_27'] += 1;
				}elseif ($datalastposition == "KM 27" || $datalastposition == "KM 27.5") {
					$dataJumlahInKmKosongan['KM_28'] += 1;
				}elseif ($datalastposition == "KM 28" || $datalastposition == "KM 28.5") {
					$dataJumlahInKmKosongan['KM_29'] += 1;
				}elseif ($datalastposition == "KM 29" || $datalastposition == "KM 29.5") {
					$dataJumlahInKmKosongan['KM_30'] += 1;
				}
			}else {
				if ($datalastposition == "Port BIB - Antrian") {
					$dataJumlahInKmMuatan_2['gb5_port_bib_antrian'] += 1;
				}elseif ($datalastposition == "Port BIR - Antrian WB") {
					$dataJumlahInKmMuatan_2['gb6_port_bir_antrian_wb'] += 1;
				}elseif ($datalastposition == "KM 1" || $datalastposition == "KM 1.5" || $datalastposition == "KM 0.5") {
					$dataJumlahInKmMuatan['KM_2'] += 1;
				}elseif ($datalastposition == "KM 2" || $datalastposition == "KM 2.5") {
					$dataJumlahInKmMuatan['KM_3'] += 1;
				}elseif ($datalastposition == "KM 3" || $datalastposition == "KM 3.5") {
					$dataJumlahInKmMuatan['KM_4'] += 1;
				}elseif ($datalastposition == "KM 4" || $datalastposition == "KM 4.5") {
					$dataJumlahInKmMuatan['KM_5'] += 1;
				}elseif ($datalastposition == "KM 5" || $datalastposition == "KM 5.5") {
					$dataJumlahInKmMuatan['KM_6'] += 1;
				}elseif ($datalastposition == "KM 6" || $datalastposition == "KM 6.5") {
					$dataJumlahInKmMuatan['KM_7'] += 1;
				}elseif ($datalastposition == "KM 7" || $datalastposition == "KM 7.5") {
					$dataJumlahInKmMuatan['KM_8'] += 1;
				}elseif ($datalastposition == "KM 8" || $datalastposition == "KM 8.5") {
					$dataJumlahInKmMuatan['KM_9'] += 1;
				}elseif ($datalastposition == "KM 9" || $datalastposition == "KM 9.5") {
					$dataJumlahInKmMuatan['KM_10'] += 1;
				}elseif ($datalastposition == "KM 10" || $datalastposition == "KM 10.5") {
					$dataJumlahInKmMuatan['KM_11'] += 1;
				}elseif ($datalastposition == "KM 11" || $datalastposition == "KM 11.5") {
					$dataJumlahInKmMuatan['KM_12'] += 1;
				}elseif ($datalastposition == "KM 12" || $datalastposition == "KM 12.5") {
					$dataJumlahInKmMuatan['KM_13'] += 1;
				}elseif ($datalastposition == "KM 13" || $datalastposition == "KM 13.5") {
					$dataJumlahInKmMuatan['KM_14'] += 1;
				}elseif ($datalastposition == "KM 14" || $datalastposition == "KM 14.5") {
					$dataJumlahInKmMuatan['KM_15'] += 1;
				}elseif ($datalastposition == "KM 15" || $datalastposition == "KM 15.5") {
					$dataJumlahInKmMuatan['KM_16'] += 1;
				}elseif ($datalastposition == "KM 16" || $datalastposition == "KM 16.5") {
					$dataJumlahInKmMuatan['KM_17'] += 1;
				}elseif ($datalastposition == "KM 17" || $datalastposition == "KM 17.5") {
					$dataJumlahInKmMuatan['KM_18'] += 1;
				}elseif ($datalastposition == "KM 18" || $datalastposition == "KM 18.5") {
					$dataJumlahInKmMuatan['KM_19'] += 1;
				}elseif ($datalastposition == "KM 19" || $datalastposition == "KM 19.5") {
					$dataJumlahInKmMuatan['KM_20'] += 1;
				}elseif ($datalastposition == "KM 20" || $datalastposition == "KM 20.5") {
					$dataJumlahInKmMuatan['KM_21'] += 1;
				}elseif ($datalastposition == "KM 21" || $datalastposition == "KM 21.5") {
					$dataJumlahInKmMuatan['KM_22'] += 1;
				}elseif ($datalastposition == "KM 22" || $datalastposition == "KM 22.5") {
					$dataJumlahInKmMuatan['KM_23'] += 1;
				}elseif ($datalastposition == "KM 23" || $datalastposition == "KM 23.5") {
					$dataJumlahInKmMuatan['KM_24'] += 1;
				}elseif ($datalastposition == "KM 24" || $datalastposition == "KM 24.5") {
					$dataJumlahInKmMuatan['KM_25'] += 1;
				}elseif ($datalastposition == "KM 25" || $datalastposition == "KM 25.5") {
					$dataJumlahInKmMuatan['KM_26'] += 1;
				}elseif ($datalastposition == "KM 26" || $datalastposition == "KM 26.5") {
					$dataJumlahInKmMuatan['KM_27'] += 1;
				}elseif ($datalastposition == "KM 27" || $datalastposition == "KM 27.5") {
					$dataJumlahInKmMuatan['KM_28'] += 1;
				}elseif ($datalastposition == "KM 28" || $datalastposition == "KM 28.5") {
					$dataJumlahInKmMuatan['KM_29'] += 1;
				}elseif ($datalastposition == "KM 29" || $datalastposition == "KM 29.5") {
					$dataJumlahInKmMuatan['KM_30'] += 1;
				}
			}
		}
	}

	// LIMIT SETTING PER KM
	$arraynotin              = array("Port BIR - Kosongan 2", "Port BIB - Kosongan 2", "Simpang Bayah - Kosongan", "Port BIR - Antrian WB", "Port BIB - Kosongan 1",
																		"Port BIB - Antrian", "Port BIR - Kosongan 1", "KM 1");

	$arraynotinAllKMKosongan = array("Port BIR - Kosongan 2", "Port BIB - Kosongan 2", "Simpang Bayah - Kosongan", "Port BIR - Antrian WB", "Port BIB - Kosongan 1",
																		"Port BIB - Antrian", "Port BIR - Kosongan 1");
  $arrayinKM1Muatan	 			 = array("KM 1");

	$getdataFromStreet = $this->m_poipoolmaster->getstreet_now(1);
	$mapSettingType    = $this->m_poipoolmaster->getMapSettingByType(1);

	$postfix_middle_limit_allkmmuatan   = "_middle_limit_allkmmuatan";
	$postfix_top_limit_allkmmuatan      = "_top_limit_allkmmuatan";
	$postfix_middle_limit_allkmkosongan = "_middle_limit_allkmkosongan";
	$postfix_top_limit_allkmkosongan    = "_top_limit_allkmkosongan";
	$postfix_bottom_limit_km1muatan     = "_bottom_limit_km1muatan";
	$postfix_middle_limit_km1muatan     = "_middle_limit_km1muatan";
	$postfix_top_limit_km1muatan        = "_top_limit_km1muatan";

	// LIMIT ONLY KM 1
	if (isset($getdataFromStreet)) {
		$datafixlimitkm1muatan = array();
		for ($i=0; $i < sizeof($getdataFromStreet); $i++) {
			$streetremovecoma         = explode(",", $getdataFromStreet[$i]['street_name']);
				if (in_array($streetremovecoma[0], $arrayinKM1Muatan)) {
					$streetfix                = str_replace(" ", "_", $streetremovecoma[0]);
					$bottomlimitname          = $streetfix.$postfix_bottom_limit_km1muatan;
					$middlelimitname          = $streetfix.$postfix_middle_limit_km1muatan;
					$toplimitname             = $streetfix.$postfix_top_limit_km1muatan;
					$getMapSettingByLimitName = $this->m_poipoolmaster->getThisMapSettingByLimitName_mapsetting_onlykm1($bottomlimitname, $middlelimitname, $toplimitname);

					// echo "<pre>";
					// var_dump($getMapSettingByLimitName);die();
					// echo "<pre>";

					if (sizeof($getMapSettingByLimitName) > 1) {
							array_push($datafixlimitkm1muatan, array(
								"street_id"               => $getdataFromStreet[$i]['street_id'],
								"street_name"             => $getdataFromStreet[$i]['street_name'],
								"mapsetting_type"         => 1,
								"mapsetting_name_alias"   => $streetremovecoma[0],
								"mapsetting_name"         => $streetfix,
								"mapsetting_bottom_limit" => $getMapSettingByLimitName[0]['mapsetting_limit_value'],
								"mapsetting_middle_limit" => $getMapSettingByLimitName[1]['mapsetting_limit_value'],
								"mapsetting_top_limit"    => $getMapSettingByLimitName[2]['mapsetting_limit_value']
							));
					}else {
						array_push($datafixlimitkm1muatan, array(
							"street_id"               => $getdataFromStreet[$i]['street_id'],
							"street_name"             => $getdataFromStreet[$i]['street_name'],
							"mapsetting_type"         => 1,
							"mapsetting_name_alias"   => $streetremovecoma[0],
							"mapsetting_name"         => $streetfix,
							"mapsetting_bottom_limit" => 0,
							"mapsetting_middle_limit" => 0,
							"mapsetting_top_limit"    => 0
						));
					}
				}
		}
	}

	// LIMIT FOR ALL KM MUATAN EXCEPT KM 1
	if (isset($getdataFromStreet)) {
		$datafixlimitperkmallmuatan = array();
		for ($i=0; $i < sizeof($getdataFromStreet); $i++) {
			$streetremovecoma         = explode(",", $getdataFromStreet[$i]['street_name']);
				if (!in_array($streetremovecoma[0], $arraynotin)) {
					$streetfix                = str_replace(" ", "_", $streetremovecoma[0]);

					$middlelimitname          = $streetfix.$postfix_middle_limit_allkmmuatan;
					$toplimitname             = $streetfix.$postfix_top_limit_allkmmuatan;

					$getMapSettingByLimitName = $this->m_poipoolmaster->getThisMapSettingByLimitName($middlelimitname, $toplimitname);

					if (sizeof($getMapSettingByLimitName) > 1) {
							array_push($datafixlimitperkmallmuatan, array(
								"street_id"               => $getdataFromStreet[$i]['street_id'],
								"street_name"             => $getdataFromStreet[$i]['street_name'],
								"mapsetting_type"         => 1,
								"mapsetting_name_alias"   => $streetremovecoma[0],
								"mapsetting_name"         => $streetfix,
								"mapsetting_middle_limit" => $getMapSettingByLimitName[0]['mapsetting_limit_value'],
								"mapsetting_top_limit"    => $getMapSettingByLimitName[1]['mapsetting_limit_value']
							));
					}else {
						array_push($datafixlimitperkmallmuatan, array(
							"street_id"               => $getdataFromStreet[$i]['street_id'],
							"street_name"             => $getdataFromStreet[$i]['street_name'],
							"mapsetting_type"         => 1,
							"mapsetting_name_alias"   => $streetremovecoma[0],
							"mapsetting_name"         => $streetfix,
							"mapsetting_middle_limit" => 0,
							"mapsetting_top_limit"    => 0
						));
					}
				}
		}
	}

	// LIMIT ALL KOSONGAN
	if (isset($getdataFromStreet)) {
		$datafixlimitperkmallkosongan = array();
		for ($i=0; $i < sizeof($getdataFromStreet); $i++) {
			$streetremovecoma         = explode(",", $getdataFromStreet[$i]['street_name']);
				if (!in_array($streetremovecoma[0], $arraynotinAllKMKosongan)) {
					$streetfix                = str_replace(" ", "_", $streetremovecoma[0]);

					$middlelimitname          = $streetfix.$postfix_middle_limit_allkmkosongan;
					$toplimitname             = $streetfix.$postfix_top_limit_allkmkosongan;

					$getMapSettingByLimitName = $this->m_poipoolmaster->getThisMapSettingByLimitName($middlelimitname, $toplimitname);

					if (sizeof($getMapSettingByLimitName) > 1) {
							array_push($datafixlimitperkmallkosongan, array(
								"street_id"               => $getdataFromStreet[$i]['street_id'],
								"street_name"             => $getdataFromStreet[$i]['street_name'],
								"mapsetting_type"         => 1,
								"mapsetting_name_alias"   => $streetremovecoma[0],
								"mapsetting_name"         => $streetfix,
								"mapsetting_middle_limit" => $getMapSettingByLimitName[0]['mapsetting_limit_value'],
								"mapsetting_top_limit"    => $getMapSettingByLimitName[1]['mapsetting_limit_value']
							));
					}else {
						array_push($datafixlimitperkmallkosongan, array(
							"street_id"               => $getdataFromStreet[$i]['street_id'],
							"street_name"             => $getdataFromStreet[$i]['street_name'],
							"mapsetting_type"         => 1,
							"mapsetting_name_alias"   => $streetremovecoma[0],
							"mapsetting_name"         => $streetfix,
							"mapsetting_middle_limit" => 0,
							"mapsetting_top_limit"    => 0
						));
					}
				}
		}
	}

	// GET LIST IN ROM & PORT
	$romType 		         = 3; //ROM TYPE
	$portType 		       = 4; //PORT TYPE
	$portTypeCPBIB 		   = 7; //portType CP BIB
	$portTypeANTBIR 		 = 8; //portType ANT BIR
	$romStreet           = $this->m_poipoolmaster->getstreet_now2($romType);
	$portStreet          = $this->m_poipoolmaster->getstreet_now2($portType);
	$portCPBIB           = $this->m_poipoolmaster->getstreet_now2($portTypeCPBIB);
	$portANTBIR          = $this->m_poipoolmaster->getstreet_now2($portTypeANTBIR);
	$dataRomFix          = array();
	$dataPortFix         = array();
	$dataPortCPBIBFix    = array();
	$dataPortANTBIRFix   = array();

	// echo "<pre>";
	// var_dump($romStreet);die();
	// echo "<pre>";

	for ($j=0; $j < sizeof($romStreet); $j++) {
		$street_name_rom                  = explode(",", $romStreet[$j]['street_name']);
		$street_nameromfix                = $street_name_rom[0];
		$dataStateRom[$street_nameromfix] = 0;

		for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
			$autocheck          = json_decode($masterdatavehicle[$i]['vehicle_autocheck']);
			$auto_last_position = explode(",", $autocheck->auto_last_position);
			$datalastposition   = $auto_last_position[0];
			$auto_status 		    = $autocheck->auto_status;

			if ($auto_status != "M") {
				if ($datalastposition == $street_nameromfix) {
						$dataStateRom[$street_nameromfix] += 1;
				}
			}
		}
	}

	for ($k=0; $k < sizeof($portStreet); $k++) {
		$street_name_port                   = explode(",", $portStreet[$k]['street_name']);
		$street_nameportfix                 = $street_name_port[0];
		$dataStatePort[$street_nameportfix] = 0;

		for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
			$autocheck          = json_decode($masterdatavehicle[$i]['vehicle_autocheck']);
			$auto_last_position = explode(",", $autocheck->auto_last_position);
			$datalastposition   = $auto_last_position[0];
			$auto_status 		    = $autocheck->auto_status;

			if ($auto_status != "M") {
				if ($datalastposition == $street_nameportfix) {
						$dataStatePort[$street_nameportfix] += 1;
				}
			}
		}
	}

	for ($l=0; $l < sizeof($portCPBIB); $l++) {
		$street_name_portCPBIB                   = explode(",", $portCPBIB[$l]['street_name']);
		$street_nameportCPBIBfix                 = $street_name_portCPBIB[0];
		$dataStatePortCPBIB[$street_nameportCPBIBfix] = 0;

		for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
			$autocheck          = json_decode($masterdatavehicle[$i]['vehicle_autocheck']);
			$auto_last_position = explode(",", $autocheck->auto_last_position);
			$datalastposition   = $auto_last_position[0];
			$auto_status 		    = $autocheck->auto_status;

			if ($auto_status != "M") {
				if ($datalastposition == $street_nameportCPBIBfix) {
						$dataStatePortCPBIB[$street_nameportCPBIBfix] += 1;
				}
			}
		}
	}

	for ($m=0; $m < sizeof($portANTBIR); $m++) {
		$street_name_portANTBIR                   = explode(",", $portANTBIR[$m]['street_name']);
		$street_nameportANTBIRfix                 = $street_name_portANTBIR[0];
		$dataStatePortANTBIR[$street_nameportANTBIRfix] = 0;

		for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
			$autocheck          = json_decode($masterdatavehicle[$i]['vehicle_autocheck']);
			$auto_last_position = explode(",", $autocheck->auto_last_position);
			$datalastposition   = $auto_last_position[0];
			$auto_status 		    = $autocheck->auto_status;

			if ($auto_status != "M") {
				if ($datalastposition == $street_nameportANTBIRfix) {
						$dataStatePortANTBIR[$street_nameportANTBIRfix] += 1;
				}
			}
		}
	}

	// echo "<pre>";
	// var_dump($dataJumlahInKmMuatan);die(); //dataJumlahInKmKosongan dataJumlahInKmMuatan
	// echo "<pre>";
	// LIMIT SETTING PER KM

	// echo "<pre>";
	// var_dump($dataStatePort);die();
	// echo "<pre>";

	echo json_encode(array("msg" => "success", "code" => 200, "dataPortCPBIB" => $dataStatePortCPBIB, "dataPortANTBIR" => $dataStatePortANTBIR, "dataRominQuickCount" => $dataStateRom, "dataPortinQuickCount" => $dataStatePort, "dataMuatan" => $dataJumlahInKmMuatan, "dataKosongan" => $dataJumlahInKmKosongan, "dataMuatan2" => $dataJumlahInKmMuatan_2, "dataKosongan2" => $dataJumlahInKmKosongan_2, "lastcheck" => $lasttimecheck, "datafixlimitperkmallmuatan" => $datafixlimitperkmallmuatan, "datafixlimitperkmallkosongan" => $datafixlimitperkmallkosongan, "datafixlimitkm1muatan" => $datafixlimitkm1muatan));
	// echo json_encode(array("msg" => "success", "code" => 200, "dataMuatan" => $dataJumlahInKmMuatan, "dataKosongan" => $dataJumlahInKmKosongan, "dataMuatan2" => $dataJumlahInKmMuatan_2, "dataKosongan2" => $dataJumlahInKmKosongan_2, "lastcheck" => $lasttimecheck, "datafixlimitperkmallmuatan" => $datafixlimitperkmallmuatan, "datafixlimitperkmallkosongan" => $datafixlimitperkmallkosongan, "datafixlimitkm1muatan" => $datafixlimitkm1muatan));
}

function getlistinkm(){
	$dataVehicleOnKosongan     = array();
	$dataVehicleOnMuatan       = array();
	$idkm 							       = $_POST['idkm'];
	$contractor 							 = $_POST['contractor'];
	$kmonsearch 					     = array();
	$masterdatavehicle         = $this->m_poipoolmaster->getmastervehiclebycontractor($contractor);

	if ($idkm == 1) {
		$kmonsearch = array("Port BIB - Antrian", "Port BIB - Kosongan 1", "Port BIB - Kosongan 2", "Port BIR - Antrian WB", "Port BIR - Kosongan 1", "Port BIR - Kosongan 2", "Simpang Bayah - Kosongan");
	}elseif ($idkm == 2) {
		$kmonsearch = array("KM 1", "KM 1.5", "KM 0.5");
	}elseif ($idkm == 3) {
		$kmonsearch = array("KM 2", "KM 2.5");
	}elseif ($idkm == 4) {
		$kmonsearch = array("KM 3", "KM 3.5");
	}elseif ($idkm == 5) {
		$kmonsearch = array("KM 4", "KM 4.5");
	}elseif ($idkm == 6) {
		$kmonsearch = array("KM 5", "KM 5.5");
	}elseif ($idkm == 7) {
		$kmonsearch = array("KM 6", "KM 6.5");
	}elseif ($idkm == 8) {
		$kmonsearch = array("KM 7", "KM 7.5");
	}elseif ($idkm == 9) {
		$kmonsearch = array("KM 8", "KM 8.5");
	}elseif ($idkm == 10) {
		$kmonsearch = array("KM 9", "KM 9.5");
	}elseif ($idkm == 11) {
		$kmonsearch = array("KM 10", "KM 10.5");
	}elseif ($idkm == 12) {
		$kmonsearch = array("KM 11", "KM 11.5");
	}elseif ($idkm == 13) {
		$kmonsearch = array("KM 12", "KM 12.5");
	}elseif ($idkm == 14) {
		$kmonsearch = array("KM 13", "KM 13.5");
	}elseif ($idkm == 15) {
		$kmonsearch = array("KM 14", "KM 14.5");
	}elseif ($idkm == 16) {
		$kmonsearch = array("KM 15", "KM 15.5");
	}elseif ($idkm == 17) {
		$kmonsearch = array("KM 16", "KM 16.5");
	}elseif ($idkm == 18) {
		$kmonsearch = array("KM 17", "KM 17.5");
	}elseif ($idkm == 19) {
		$kmonsearch = array("KM 18", "KM 18.5");
	}elseif ($idkm == 20) {
		$kmonsearch = array("KM 19", "KM 19.5");
	}elseif ($idkm == 21) {
		$kmonsearch = array("KM 20", "KM 20.5");
	}elseif ($idkm == 22) {
		$kmonsearch = array("KM 21", "KM 21.5");
	}elseif ($idkm == 23) {
		$kmonsearch = array("KM 22", "KM 22.5");
	}elseif ($idkm == 24) {
		$kmonsearch = array("KM 23", "KM 23.5");
	}elseif ($idkm == 25) {
		$kmonsearch = array("KM 24", "KM 24.5");
	}elseif ($idkm == 26) {
		$kmonsearch = array("KM 25", "KM 25.5");
	}elseif ($idkm == 27) {
		$kmonsearch = array("KM 26", "KM 26.5");
	}elseif ($idkm == 28) {
		$kmonsearch = array("KM 27", "KM 27.5");
	}elseif ($idkm == 29) {
		$kmonsearch = array("KM 28", "KM 28.5");
	}elseif ($idkm == 30) {
		$kmonsearch = array("KM 29", "KM 29.5");
	}

	$vCompanyFix 					 = "KM " . $idkm;

	// echo "<pre>";
	// var_dump($vCompany);die();
	// echo "<pre>";

	for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
		$autocheck          = json_decode($masterdatavehicle[$i]['vehicle_autocheck']);
		$jalur_name         = $autocheck->auto_last_road;
		$auto_last_position = explode(",", $autocheck->auto_last_position);
		$datalastposition   = $auto_last_position[0];

		// echo "<pre>";
		// var_dump($datalastposition);die();
		// echo "<pre>";

			if (in_array($datalastposition, $kmonsearch)) {
				if ($jalur_name == "kosongan") {
					array_push($dataVehicleOnKosongan, array(
						"vehicle_no"            => $masterdatavehicle[$i]['vehicle_no'],
						"vehicle_name"          => $masterdatavehicle[$i]['vehicle_name'],
						"auto_last_lat"         => $autocheck->auto_last_lat,
						"auto_last_long"        => $autocheck->auto_last_long,
						"auto_last_positionfix" => $datalastposition,
						"auto_last_engine"      => $autocheck->auto_last_engine,
						"auto_last_speed"       => $autocheck->auto_last_speed,
						"auto_last_update"      => date("d-m-Y H:i:s", strtotime($autocheck->auto_last_update))
					));
				}else {
					array_push($dataVehicleOnMuatan, array(
						"vehicle_no"            => $masterdatavehicle[$i]['vehicle_no'],
						"vehicle_name"          => $masterdatavehicle[$i]['vehicle_name'],
						"auto_last_lat"         => $autocheck->auto_last_lat,
						"auto_last_long"        => $autocheck->auto_last_long,
						"auto_last_positionfix" => $datalastposition,
						"auto_last_engine"      => $autocheck->auto_last_engine,
						"auto_last_speed"       => $autocheck->auto_last_speed,
						"auto_last_update"      => date("d-m-Y H:i:s", strtotime($autocheck->auto_last_update))
					));
				}
			}
	}

	// echo "<pre>";
	// var_dump($dataVehicleOnKosongan);die();
	// echo "<pre>";

	echo json_encode(array("msg" => "success", "code" => 200, "dataKosongan" => $dataVehicleOnKosongan, "dataMuatan" => $dataVehicleOnMuatan, "kmsent" => $vCompanyFix));
}

function port_quickcount(){
	$masterdatavehicle            = $this->m_poipoolmaster->getmastervehicle();
	$dataPortFix                  = array();
	$dataPort['port_bbc'] = 0;
	$dataPort['port_bib'] = 0;
	$dataPort['port_bir'] = 0;
	$dataPort['port_tia'] = 0;

	for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
		$autocheck          = json_decode($masterdatavehicle[$i]['vehicle_autocheck']);
		$auto_last_position = explode(",", $autocheck->auto_last_position);
		$datalastposition   = $auto_last_position[0];

		if ($datalastposition == "PORT BBC") {
			$dataPort['port_bbc']  += 1;
		}elseif ($datalastposition == "PORT BIB") {
			$dataPort['port_bib']  += 1;
		}elseif ($datalastposition == "PORT BIR") {
			$dataPort['port_bir']  += 1;
		}elseif ($datalastposition == "PORT TIA") {
			$dataPort['port_tia']  += 1;
		}
	}

	// echo "<pre>";
	// var_dump($dataPort);die();
	// echo "<pre>";

	echo json_encode(array("msg" => "success", "code" => 200, "data" => $dataPort));
}

function getlistinport(){
	$allCompany 					  = $this->m_poipoolmaster->getAllCompany();
	$dataContractor['BKAE'] = 0;
	$dataContractor['KMB']  = 0;
	$dataContractor['GECL'] = 0;
	$dataContractor['BMT'] = 0;
	$dataContractor['RAMB'] = 0;
	$dataContractor['BBS']  = 0;
	$dataContractor['MKS']  = 0;
	$dataContractor['RBT']  = 0;
	$dataContractor['MMS']  = 0;
	$dataContractor['EST']  = 0;

	$dataVehicleOnPort         = array();
	$idport 							     = $_POST['idport'];
	$contractor 							 = $_POST['contractor'];
	$masterdatavehicle         = $this->m_poipoolmaster->getmastervehiclebycontractor($contractor);

	$vCompany 					     = $this->dashboardmodel->getstreet_id("", $idport);
	$streetFix 					     = explode(",", $vCompany->street_name);
	$street_alias 					 = explode(",", $vCompany->street_alias);

	for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
		$autocheck          = json_decode($masterdatavehicle[$i]['vehicle_autocheck']);
		$auto_last_position = explode(",", $autocheck->auto_last_position);
		$datalastposition   = $auto_last_position[0];

		// echo "<pre>";
		// var_dump($autocheck);die();
		// echo "<pre>";

			if ($datalastposition == $streetFix[0]) {
				array_push($dataVehicleOnPort, array(
					"vehicle_no"       => $masterdatavehicle[$i]['vehicle_no'],
					"vehicle_name"     => $masterdatavehicle[$i]['vehicle_name'],
					"vehicle_company"  => $masterdatavehicle[$i]['vehicle_company'],
					"auto_last_status" => $autocheck->auto_status,
					"auto_last_lat"    => $autocheck->auto_last_lat,
					"auto_last_long"   => $autocheck->auto_last_long,
					"auto_last_engine" => $autocheck->auto_last_engine,
					"auto_last_speed"  => $autocheck->auto_last_speed,
					"auto_last_update" => date("d-m-Y H:i:s", strtotime($autocheck->auto_last_update))
				));
			}
	}

	for ($k=0; $k < sizeof($dataVehicleOnPort); $k++) {
		for ($j=0; $j < sizeof($allCompany); $j++) {
			if ($dataVehicleOnPort[$k]['vehicle_company'] == $allCompany[$j]['company_id']) {
				if ($allCompany[$j]['company_name'] == "BKAE") {
					$dataContractor['BKAE'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "KMB") {
					$dataContractor['KMB'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "GECL") {
					$dataContractor['GECL'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "BMT") {
					$dataContractor['BMT'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "RAMB") {
					$dataContractor['RAMB'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "BBS") {
					$dataContractor['BBS'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "MKS") {
					$dataContractor['MKS'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "RBT") {
					$dataContractor['RBT'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "MMS") {
					$dataContractor['MMS'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "EST") {
					$dataContractor['EST'] += 1;
				}
			}
		}
	}

	// echo "<pre>";
	// var_dump($dataContractor);die();
	// echo "<pre>";
	echo json_encode(array("msg" => "success", "code" => 200, "allCompany" => $allCompany, "jumlah_contractor" => $dataContractor, "data" => $dataVehicleOnPort, "portsent" => $street_alias));
}

function rom_quickcount(){
	$masterdatavehicle       = $this->m_poipoolmaster->getmastervehicle();
	$dataRomFix              = array();
	$dataRom['rom_1']        = 0;
	$dataRom['rom_b1_road']  = 0;
	$dataRom['rom_2']        = 0;
	$dataRom['rom_b1']       = 0;
	$dataRom['rom_b3']       = 0;
	$dataRom['rom_b2_road']  = 0;
	$dataRom['rom_4']        = 0;
	$dataRom['rom_6']        = 0;
	$dataRom['rom_6_road']   = 0;
	$dataRom['rom_7']        = 0;
	$dataRom['rom_7_8_road'] = 0;
	$dataRom['rom_8']        = 0;
	$dataRom['rom_a1']       = 0;
	$dataRom['rom_10']       = 0;

	for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
		$autocheck          = json_decode($masterdatavehicle[$i]['vehicle_autocheck']);
		$auto_last_position = explode(",", $autocheck->auto_last_position);
		$datalastposition   = $auto_last_position[0];

			if ($datalastposition == "ROM 01") {
				$dataRom['rom_1']  += 1;
			}elseif ($datalastposition == "ROM B1") {
				$dataRom['rom_b1']  += 1;
			}elseif ($datalastposition == "ROM B1 ROAD") {
				$dataRom['rom_b1_road']  += 1;
			}elseif ($datalastposition == "ROM 02") {
				$dataRom['rom_2']  += 1;
			}elseif ($datalastposition == "ROM B3") {
				$dataRom['rom_b3']  += 1;
			}elseif ($datalastposition == "ROM B2 ROAD") {
				$dataRom['rom_b2_road']  += 1;
			}elseif ($datalastposition == "ROM B2") {
				$dataRom['rom_4']  += 1;
			}elseif ($datalastposition == "ROM 06 ROAD") {
				$dataRom['rom_6_road']  += 1;
			}elseif ($datalastposition == "ROM 06") {
				$dataRom['rom_6']  += 1;
			}elseif ($datalastposition == "ROM 07") {
				$dataRom['rom_7']  += 1;
			}elseif ($datalastposition == "ROM 07/08 ROAD") {
				$dataRom['rom_7_8_road']  += 1;
			}elseif ($datalastposition == "ROM 08") {
				$dataRom['rom_8']  += 1;
			}elseif ($datalastposition == "ROM A1") {
				$dataRom['rom_a1']  += 1;
			}elseif ($datalastposition == "ROM 10") {
				$dataRom['rom_10']  += 1;
			}
	}

	// echo "<pre>";
	// var_dump($dataRom);die();
	// echo "<pre>";

	echo json_encode(array("msg" => "success", "code" => 200, "data" => $dataRom));
}

function getlistinrom(){
	$allCompany 					  = $this->m_poipoolmaster->getAllCompany();
	$dataContractor['BKAE'] = 0;
	$dataContractor['KMB']  = 0;
	$dataContractor['GECL'] = 0;
	$dataContractor['STLI'] = 0;
	$dataContractor['RAMB'] = 0;
	$dataContractor['BBS']  = 0;
	$dataContractor['MKS']  = 0;
	$dataContractor['RBT']  = 0;
	$dataContractor['MMS']  = 0;
	$dataContractor['EST']  = 0;

	$dataVehicleOnRom    = array();
	$idrom 							 = $_POST['idrom'];
	$contractor 				 = $_POST['contractor'];
	$masterdatavehicle   = $this->m_poipoolmaster->getmastervehiclebycontractor($contractor);

	$vCompany 					   = $this->dashboardmodel->getstreet_id("", $idrom);
	$streetFix 					 = explode(",", $vCompany->street_name);

	for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
		$autocheck          = json_decode($masterdatavehicle[$i]['vehicle_autocheck']);
		$auto_last_position = explode(",", $autocheck->auto_last_position);
		$datalastposition   = $auto_last_position[0];

		// echo "<pre>";
		// var_dump($autocheck);die();
		// echo "<pre>";

			if ($datalastposition == $streetFix[0]) {
				array_push($dataVehicleOnRom, array(
					"vehicle_no"       => $masterdatavehicle[$i]['vehicle_no'],
					"vehicle_name"     => $masterdatavehicle[$i]['vehicle_name'],
					"vehicle_company"  => $masterdatavehicle[$i]['vehicle_company'],
					"auto_last_status" => $autocheck->auto_status,
					"auto_last_lat"    => $autocheck->auto_last_lat,
					"auto_last_long"   => $autocheck->auto_last_long,
					"auto_last_engine" => $autocheck->auto_last_engine,
					"auto_last_speed"  => $autocheck->auto_last_speed,
					"auto_last_update" => date("d-m-Y H:i:s", strtotime($autocheck->auto_last_update))
				));
			}
	}

	for ($k=0; $k < sizeof($dataVehicleOnRom); $k++) {
		for ($j=0; $j < sizeof($allCompany); $j++) {
			if ($dataVehicleOnRom[$k]['vehicle_company'] == $allCompany[$j]['company_id']) {
				if ($allCompany[$j]['company_name'] == "BKAE") {
					$dataContractor['BKAE'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "KMB") {
					$dataContractor['KMB'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "GECL") {
					$dataContractor['GECL'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "STLI") {
					$dataContractor['STLI'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "RAMB") {
					$dataContractor['RAMB'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "BBS") {
					$dataContractor['BBS'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "MKS") {
					$dataContractor['MKS'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "RBT") {
					$dataContractor['RBT'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "MMS") {
					$dataContractor['MMS'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "EST") {
					$dataContractor['EST'] += 1;
				}
			}
		}
	}

	// echo "<pre>";
	// var_dump($dataContractor);die();
	// echo "<pre>";
	echo json_encode(array("msg" => "success", "code" => 200, "allCompany" => $allCompany, "jumlah_contractor" => $dataContractor, "data" => $dataVehicleOnRom, "romsent" => $streetFix[0]));
}

function poolws_quickcount(){
	$masterdatavehicle              = $this->m_poipoolmaster->getmastervehicle();
	$dataPoolFix                    = array();
	$dataPool['pool_bbs']           = 0;
	$dataPool['pool_bka']           = 0;
	$dataPool['pool_bsl']           = 0;
	$dataPool['pool_gecl']          = 0;
	$dataPool['pool_gecl2']         = 0;
	$dataPool['pool_kusan_bawah']   = 0;
	$dataPool['pool_kusan']         = 0;
	$dataPool['pool_mks']           = 0;
	$dataPool['pool_ram']           = 0;
	$dataPool['pool_rbt']           = 0;
	$dataPool['pool_rbt_brd']       = 0;
	$dataPool['pool_stli']          = 0;
	$dataPool['ws_gecl']            = 0;
	$dataPool['ws_gecl2']  				  = 0;
	$dataPool['ws_gecl3']  				  = 0;
	$dataPool['ws_kmb']             = 0;
	$dataPool['ws_kmb_induk']       = 0;
	$dataPool['ws_mks']             = 0;
	$dataPool['ws_rbt']             = 0;
	$dataPool['ws_mms']             = 0;
	$dataPool['ws_est']             = 0;
	$dataPool['ws_bbb']             = 0;

		for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
			$autocheck          = json_decode($masterdatavehicle[$i]['vehicle_autocheck']);
			$auto_last_position = explode(",", $autocheck->auto_last_position);
			$datalastposition   = $auto_last_position[0];

				if ($datalastposition == "POOL BBS") {
					$dataPool['pool_bbs'] += 1;
				}elseif ($datalastposition == "POOL BKA") {
					$dataPool['pool_bka'] += 1;
				}elseif ($datalastposition == "POOL BSL") {
					$dataPool['pool_bsl'] += 1;
				}elseif ($datalastposition == "POOL GECL") {
					$dataPool['pool_gecl'] += 1;
				}elseif ($datalastposition == "POOL GECL 2") {
					$dataPool['pool_gecl2'] += 1;
				}elseif ($datalastposition == "POOL KUSAN BAWAH") {
					$dataPool['pool_kusan_bawah'] += 1;
				}elseif ($datalastposition == "POOL KUSAN") {
					$dataPool['pool_kusan'] += 1;
				}elseif ($datalastposition == "POOL MKS") {
					$dataPool['pool_mks'] += 1;
				}elseif ($datalastposition == "POOL RAM") {
					$dataPool['pool_ram'] += 1;
				}elseif ($datalastposition == "POOL RBT") {
					$dataPool['pool_rbt'] += 1;
				}elseif ($datalastposition == "POOL RBT BRD") {
					$dataPool['pool_rbt_brd'] += 1;
				}elseif ($datalastposition == "POOL STLI") {
					$dataPool['pool_stli'] += 1;
				}elseif ($datalastposition == "WS GECL") {
					$dataPool['ws_gecl'] += 1;
				}elseif ($datalastposition == "WS GECL 2") {
					$dataPool['ws_gecl2'] += 1;
				}elseif ($datalastposition == "WS GECL 3") {
					$dataPool['ws_gecl3'] += 1;
				}elseif ($datalastposition == "WS KMB") {
					$dataPool['ws_kmb'] += 1;
				}elseif ($datalastposition == "WS KMB INDUK") {
					$dataPool['ws_kmb_induk'] += 1;
				}elseif ($datalastposition == "WS MKS") {
					$dataPool['ws_mks'] += 1;
				}elseif ($datalastposition == "WS RBT") {
					$dataPool['ws_rbt'] += 1;
				}elseif ($datalastposition == "WS MMS") {
					$dataPool['ws_mms'] += 1;
				}elseif ($datalastposition == "WS EST") {
					$dataPool['ws_est'] += 1;
				}elseif ($datalastposition == "WS BBB") {
					$dataPool['ws_bbb'] += 1;
				}
		}

		// echo "<pre>";
		// var_dump($dataPool);die();
		// echo "<pre>";

		echo json_encode(array("msg" => "success", "code" => 200, "data" => $dataPool));
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
	$user_id      = $this->sess->user_id;
	$user_company = $this->sess->user_company;
	$user_parent  = $this->sess->user_parent;
	$user_id_role = $this->sess->user_id_role;

		if ($user_id_role == 0) {
			$this->db->where("company_created_by", $user_id);
		}elseif ($user_id_role == 1) {
			$this->db->where("company_created_by", $user_parent);
		}elseif ($user_id_role == 2) {
			$this->db->where("company_created_by", $user_parent);
		}elseif ($user_id_role == 3) {
			$this->db->where("company_created_by", $user_parent);
		}elseif ($user_id_role == 4) {
			$this->db->where("company_created_by", $user_parent);
		}elseif ($user_id_role == 5) {
			$this->db->where("company_id", $user_company);
		}elseif ($user_id_role == 6) {
			$this->db->where("company_id", $user_company);
		}

	$this->db->where("company_flag", 0);
	$this->db->where_in("company_exca", array(0, 2));
	$this->db->order_by("company_name", "ASC");
	$q     = $this->db->get("company");
	$rows  = $q->result_array();

	// echo "<pre>";
	// var_dump($rows);die();
	// echo "<pre>";

	echo json_encode(array("msg" => "success", "code" => 200, "data" => $rows));
}

function getdatacontractor_hrm(){
	$user_id      = $this->sess->user_id;
	$user_company = $this->sess->user_company;
	$user_parent  = $this->sess->user_parent;
	$user_id_role = $this->sess->user_id_role;

		if ($user_id_role == 0) {
			$this->db->where("company_id", 1963);
		}elseif ($user_id_role == 1) {
			$this->db->where("company_id", 1963);
		}elseif ($user_id_role == 2) {
			$this->db->where("company_id", 1963);
		}elseif ($user_id_role == 3) {
			$this->db->where("company_id", 1963);
		}elseif ($user_id_role == 4) {
			$this->db->where("company_id", 1963);
		}

	$this->db->where("company_flag", 0);
	$this->db->where_in("company_exca", array(0, 2));
	$this->db->order_by("company_name", "ASC");
	$q     = $this->db->get("company");
	$rows  = $q->result_array();

	// echo "<pre>";
	// var_dump($rows);die();
	// echo "<pre>";

	echo json_encode(array("msg" => "success", "code" => 200, "data" => $rows));
}

function vehicleByContractorheatmap(){
	$user_id         = $this->sess->user_id;
	$user_parent     = $this->sess->user_parent;
	$privilegecode   = $this->sess->user_id_role;
	$user_company    = $this->sess->user_company;
	$companyid       = $this->input->post('companyid');
	$valueMapsOption = $this->input->post('valuemapsoption');

	$this->db->select("*");
	$this->db->where("vehicle_status <>", 3);
	$this->db->where("vehicle_gotohistory", 0);
	// $this->db->where("vehicle_autocheck is not NULL");

		if ($companyid == 0) {
			if ($privilegecode == 0) {
				$this->db->where("vehicle_user_id", $user_id);
			}elseif ($privilegecode == 1) {
				$this->db->where("vehicle_user_id", $user_parent);
			}elseif ($privilegecode == 2) {
				$this->db->where("vehicle_user_id", $user_parent);
			}elseif ($privilegecode == 3) {
				$this->db->where("vehicle_user_id", $user_parent);
			}elseif ($privilegecode == 4) {
				$this->db->where("vehicle_user_id", $user_parent);
			}elseif ($privilegecode == 5) {
				$this->db->where("vehicle_company", $user_company);
				// $this->db->or_where("vehicle_id_shareto", $companyid);
			}elseif ($privilegecode == 6) {
				$this->db->where("vehicle_company", $user_company);
				// $this->db->or_where("vehicle_id_shareto", $companyid);
			}
		}else {
			$this->db->where("vehicle_company", $companyid);
			// $this->db->or_where("vehicle_id_shareto", $companyid);
		}

	$this->db->order_by("vehicle_no", "ASC");
	$q    = $this->db->get("vehicle");
	$rows = $q->result_array();

	if ($valueMapsOption == 1) {
		$poolmaster        = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");
		$datavehicle       = array();

			for ($i=0; $i < sizeof($rows); $i++) {
				$autocheck         = json_decode($rows[$i]['vehicle_autocheck']);
						array_push($datavehicle, array(
							"vehicle_id"         => $rows[$i]['vehicle_id'],
							"vehicle_no"         => $rows[$i]['vehicle_no'],
							"vehicle_name"       => $rows[$i]['vehicle_name'],
							"vehicle_device"     => $rows[$i]['vehicle_device'],
							"vehicle_company"    => $rows[$i]['vehicle_company'],
							"vehicle_id_shareto" => $rows[$i]['vehicle_id_shareto'],
							"auto_last_lat"      => $autocheck->auto_last_lat,
							"auto_last_long"     => $autocheck->auto_last_long
						));
			}
			echo json_encode(array("msg" => "success", "code" => 200, "data" => $rows, "datavehicle" => $datavehicle, "poolmaster" => $poolmaster));
	}else {
		echo json_encode(array("msg" => "success", "code" => 200, "data" => $rows));
	}

	// echo "<pre>";
	// var_dump($datavehicle);die();
	// echo "<pre>";

}

function vehicleByContractor(){
	$user_id         = $this->sess->user_id;
	$user_parent     = $this->sess->user_parent;
	$privilegecode   = $this->sess->user_id_role;
	$user_company    = $this->sess->user_company;
	$companyid       = $this->input->post('companyid');
	$valueMapsOption = $this->input->post('valuemapsoption');

	// echo "<pre>";
	// var_dump($user_id.'-'.$companyid.'-'.$user_company);die();
	// echo "<pre>";

	$this->db = $this->load->database("default", true);
	$this->db->select("*");
	$this->db->where("vehicle_status <>", 3);
	$this->db->where("vehicle_gotohistory", 0);
	// $this->db->where("vehicle_autocheck is not NULL");

		if ($companyid == 0) {
			if ($privilegecode == 0) {
				$this->db->where("vehicle_user_id", $user_id);
			}elseif ($privilegecode == 1) {
				$this->db->where("vehicle_user_id", $user_parent);
			}elseif ($privilegecode == 2) {
				$this->db->where("vehicle_user_id", $user_parent);
			}elseif ($privilegecode == 3) {
				$this->db->where("vehicle_user_id", $user_parent);
			}elseif ($privilegecode == 4) {
				$this->db->where("vehicle_user_id", $user_parent);
			}elseif ($privilegecode == 5) {
				$this->db->where("vehicle_company", $user_company);
				// $this->db->or_where("vehicle_id_shareto", $companyid);
			}elseif ($privilegecode == 6) {
				$this->db->where("vehicle_company", $user_company);
				// $this->db->or_where("vehicle_id_shareto", $companyid);
			}
		}else {
			$this->db->where("vehicle_company", $companyid);
			$this->db->where("vehicle_user_id", 4408);
			// $this->db->or_where("vehicle_id_shareto", $companyid);
		}

	$this->db->order_by("vehicle_no", "ASC");
	$q    = $this->db->get("vehicle");
	$rows = $q->result_array();

	// echo "<pre>";
	// var_dump($rows);die();
	// echo "<pre>";

	if ($valueMapsOption == 1) {
		$poolmaster        = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");
		$datavehicle       = array();

			for ($i=0; $i < sizeof($rows); $i++) {
				$autocheck         = json_decode($rows[$i]['vehicle_autocheck']);
						array_push($datavehicle, array(
							"vehicle_id"     => $rows[$i]['vehicle_id'],
							"vehicle_no"     => $rows[$i]['vehicle_no'],
							"vehicle_name"   => $rows[$i]['vehicle_name'],
							"vehicle_device" => $rows[$i]['vehicle_device'],
							"auto_last_lat"  => $autocheck->auto_last_lat,
							"auto_last_long" => $autocheck->auto_last_long
						));
			}
			echo json_encode(array("msg" => "success", "code" => 200, "data" => $rows, "datavehicle" => $datavehicle, "poolmaster" => $poolmaster));
	}else {
		echo json_encode(array("msg" => "success", "code" => 200, "data" => $rows));
	}

	// echo "<pre>";
	// var_dump($datavehicle);die();
	// echo "<pre>";

}

function getvehiclebycontractor(){
	$user_id       = $this->sess->user_id;
	$user_parent   = $this->sess->user_parent;
	$privilegecode = $this->sess->user_id_role;
	$user_company  = $this->sess->user_company;
	$companyid     = $this->input->post('companyid');

	$this->db->select("*");

	$this->db->where("vehicle_status <>", 3);
	$this->db->where("vehicle_gotohistory", 0);
	$this->db->where("vehicle_autocheck is not NULL");

		if ($companyid == 0) {
			if ($privilegecode == 0) {
				$this->db->where("vehicle_user_id", $user_id);
			}elseif ($privilegecode == 1) {
				$this->db->where("vehicle_user_id", $user_parent);
			}elseif ($privilegecode == 2) {
				$this->db->where("vehicle_user_id", $user_parent);
			}elseif ($privilegecode == 3) {
				$this->db->where("vehicle_user_id", $user_parent);
			}elseif ($privilegecode == 4) {
				$this->db->where("vehicle_user_id", $user_parent);
			}elseif ($privilegecode == 5) {
				$this->db->where("vehicle_company", $user_company);
				// $this->db->or_where("vehicle_id_shareto", $companyid);
			}elseif ($privilegecode == 6) {
				$this->db->where("vehicle_company", $user_company);
			}
		}else {
			$this->db->where("vehicle_company", $companyid);
			// $this->db->or_where("vehicle_id_shareto", $companyid);
		}

	$this->db->order_by("vehicle_no", "ASC");
	$q    = $this->db->get("vehicle");
	$rows = $q->result_array();

	// echo "<pre>";
	// var_dump($rows);die();
	// echo "<pre>";

	echo json_encode(array("msg" => "success", "code" => 200, "data" => $rows));
}

function bibmapsoverlay(){
	ini_set('max_execution_time', '300');
	set_time_limit(300);
	if (! isset($this->sess->user_type))
	{
		redirect(base_url());
	}

	$user_id       = $this->sess->user_id;
	$user_parent   = $this->sess->user_parent;
	$privilegecode = $this->sess->user_id_role;

	if($privilegecode == 0){
		$user_id_fix = $user_id;
	}elseif ($privilegecode == 1) {
		$user_id_fix = $user_parent;
	}elseif ($privilegecode == 2) {
		$user_id_fix = $user_parent;
	}elseif ($privilegecode == 3) {
		$user_id_fix = $user_parent;
	}elseif ($privilegecode == 5) {
		$user_id_fix = $user_id;
	}elseif ($privilegecode == 6) {
		$user_id_fix = $user_id;
	}else{
		$user_id_fix = $user_id;
	}

	$companyid                       = $this->sess->user_company;
	$user_dblive                     = $this->sess->user_dblive;
	$mastervehicle                   = $this->m_poipoolmaster->getmastervehicle();

	$datafix                         = array();
	$deviceidygtidakada              = array();

	for ($i=0; $i < sizeof($mastervehicle); $i++) {
		$jsonautocheck = json_decode($mastervehicle[$i]['vehicle_autocheck']);

		array_push($datafix, array(
			"vehicle_id"             => $mastervehicle[$i]['vehicle_id'],
			"vehicle_user_id"        => $mastervehicle[$i]['vehicle_user_id'],
			"vehicle_device"         => $mastervehicle[$i]['vehicle_device'],
			"vehicle_no"             => $mastervehicle[$i]['vehicle_no'],
			"vehicle_name"           => $mastervehicle[$i]['vehicle_name'],
			"vehicle_active_date2"   => $mastervehicle[$i]['vehicle_active_date2'],
			"auto_last_lat"          => substr($jsonautocheck->auto_last_lat, 0, 10),
			"auto_last_long"         => substr($jsonautocheck->auto_last_long, 0, 10),
		));
	}

	$company                  = $this->dashboardmodel->getcompany_byowner($privilegecode);
		if ($company) {

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
		}else {
			$this->params['company']   = 0;
			$this->params['companyid'] = 0;
			$this->params['vehicle']   = 0;
		}

	// echo "<pre>";
	// var_dump($company);die();
	// echo "<pre>";


	$this->params['url_code_view']  = "1";
	$this->params['code_view_menu'] = "monitor";
	$this->params['maps_code']      = "morehundred";

	$rstatus                        = $this->dashboardmodel->gettotalstatus($this->sess->user_id);

	$datastatus                     = explode("|", $rstatus);
	$this->params['total_online']   = $datastatus[0]+$datastatus[1]; //p + K
	$this->params['total_vehicle']  = $datastatus[3];
	$this->params['total_offline']  = $datastatus[2];

	$this->params['vehicledata']  = $datafix;
	$this->params['vehicletotal'] = sizeof($mastervehicle);
	$this->params['poolmaster']   = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");
	$getvehicle_byowner           = $this->dashboardmodel->getvehicle_byowner();
	// echo "<pre>";
	// var_dump($getvehicle_byowner);die();
	// echo "<pre>";
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
	$this->params['mapsetting']     = $this->m_poipoolmaster->getmapsetting();

	// echo "<pre>";
	// var_dump($this->params['mapsetting']);die();
	// echo "<pre>";

	$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
	$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
	$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
	$this->params["content"]        = $this->load->view('newdashboard/trackers/maps_overlay_testing', $this->params, true);
	$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
}

function heatmapoprekan(){
	ini_set('max_execution_time', '300');
	set_time_limit(300);
	if (! isset($this->sess->user_type))
	{
		redirect(base_url());
	}

	$user_id       = $this->sess->user_id;
	$user_parent   = $this->sess->user_parent;
	$privilegecode = $this->sess->user_id_role;

	if($privilegecode == 0){
		$user_id_fix = $user_id;
	}elseif ($privilegecode == 1) {
		$user_id_fix = $user_parent;
	}elseif ($privilegecode == 2) {
		$user_id_fix = $user_parent;
	}elseif ($privilegecode == 3) {
		$user_id_fix = $user_parent;
	}elseif ($privilegecode == 5) {
		$user_id_fix = $user_id;
	}elseif ($privilegecode == 6) {
		$user_id_fix = $user_id;
	}else{
		$user_id_fix = $user_id;
	}

	$statusvehicle['engine_on']  = 0;
	$statusvehicle['engine_off'] = 0;

	$companyid                       = $this->sess->user_company;
	$user_dblive                     = $this->sess->user_dblive;
	$mastervehicle                   = $this->m_poipoolmaster->getmastervehicle();

	$datafix                         = array();
	$deviceidygtidakada              = array();

	for ($i=0; $i < sizeof($mastervehicle); $i++) {
		$jsonautocheck = json_decode($mastervehicle[$i]['vehicle_autocheck']);

		if ($jsonautocheck->auto_last_engine == "ON") {
			$statusvehicle['engine_on'] += 1;
		}else {
			$statusvehicle['engine_off'] += 1;
		}

		if($jsonautocheck->auto_status != "M" ){
			array_push($datafix, array(
				"vehicle_id"             => $mastervehicle[$i]['vehicle_id'],
				"vehicle_user_id"        => $mastervehicle[$i]['vehicle_user_id'],
				"vehicle_device"         => $mastervehicle[$i]['vehicle_device'],
				"vehicle_no"             => $mastervehicle[$i]['vehicle_no'],
				"vehicle_name"           => $mastervehicle[$i]['vehicle_name'],
				"vehicle_active_date2"   => $mastervehicle[$i]['vehicle_active_date2'],
				"auto_last_lat"          => substr($jsonautocheck->auto_last_lat, 0, 10),
				"auto_last_long"         => substr($jsonautocheck->auto_last_long, 0, 10),
			));
		}
	}

	$this->params['engine_on']      = $statusvehicle['engine_on'];
	$this->params['engine_off']     = $statusvehicle['engine_off'];

	$company                  = $this->dashboardmodel->getcompany_byowner($privilegecode);
		if ($company) {

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
		}else {
			$this->params['company']   = 0;
			$this->params['companyid'] = 0;
			$this->params['vehicle']   = 0;
		}

	// echo "<pre>";
	// var_dump($company);die();
	// echo "<pre>";


	$this->params['url_code_view']  = "1";
	$this->params['code_view_menu'] = "monitor";
	$this->params['maps_code']      = "morehundred";

	$rstatus                        = $this->dashboardmodel->gettotalstatus($this->sess->user_id);

	$datastatus                     = explode("|", $rstatus);
	$this->params['total_online']   = $datastatus[0]+$datastatus[1]; //p + K
	$this->params['total_vehicle']  = $datastatus[3];
	$this->params['total_offline']  = $datastatus[2];

	$this->params['vehicledata']  = $datafix;
	$this->params['vehicletotal'] = sizeof($mastervehicle);
	$this->params['poolmaster']   = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");
	$getvehicle_byowner           = $this->dashboardmodel->getvehicle_byowner();
	// echo "<pre>";
	// var_dump($getvehicle_byowner);die();
	// echo "<pre>";
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
	$this->params['mapsetting']     = $this->m_poipoolmaster->getmapsetting();
	$this->params['poolmaster']     = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");


	// echo "<pre>";
	// var_dump($this->params['mapsetting']);die();
	// echo "<pre>";

	$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
	$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/trackers/maps_view_heatmapoprekan', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		}elseif ($privilegecode == 2) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/trackers/maps_view_heatmapoprekan', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
		}elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/trackers/maps_view_heatmapoprekan', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		}elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/trackers/maps_view_heatmapoprekan2', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		}elseif ($privilegecode == 5) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/trackers/maps_view_heatmapoprekan', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
		}elseif ($privilegecode == 6) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/trackers/maps_view_heatmapoprekan', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
		}else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/trackers/maps_view_heatmapoprekan', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
}

function mapsstandard(){ // maps with overlay seperti di history map
	if (! isset($this->sess->user_type))
	{
		redirect(base_url());
	}

	$user_id       = $this->sess->user_id;
	$user_parent   = $this->sess->user_parent;
	$privilegecode = $this->sess->user_id_role;

	if($privilegecode == 0){
		$user_id_fix = $user_id;
	}elseif ($privilegecode == 1) {
		$user_id_fix = $user_parent;
	}elseif ($privilegecode == 2) {
		$user_id_fix = $user_parent;
	}elseif ($privilegecode == 3) {
		$user_id_fix = $user_parent;
	}elseif ($privilegecode == 4) {
		$user_id_fix = $user_parent;
	}elseif ($privilegecode == 5) {
		$user_id_fix = $user_id;
	}elseif ($privilegecode == 6) {
		$user_id_fix = $user_id;
	}else{
		$user_id_fix = $user_id;
	}

	$companyid                       = $this->sess->user_company;
	$user_dblive                     = $this->sess->user_dblive;
	$companyid 											 = $_POST['companyid'];
	$forclearmaps                    = $this->m_poipoolmaster->getmastervehicle();
	$mastervehicle                   = $this->m_poipoolmaster->getmastervehiclebycontractor($companyid);

	$datafix                         = array();
	$deviceidygtidakada              = array();

	for ($i=0; $i < sizeof($mastervehicle); $i++) {
		$jsonautocheck = json_decode($mastervehicle[$i]['vehicle_autocheck']);
		$auto_status = $jsonautocheck->auto_status;

			if ($auto_status != "M") {
				array_push($datafix, array(
					"vehicle_id"           => $mastervehicle[$i]['vehicle_id'],
					"vehicle_user_id"      => $mastervehicle[$i]['vehicle_user_id'],
					"vehicle_device"       => $mastervehicle[$i]['vehicle_device'],
					"vehicle_no"           => $mastervehicle[$i]['vehicle_no'],
					"vehicle_name"         => $mastervehicle[$i]['vehicle_name'],
					"vehicle_active_date2" => $mastervehicle[$i]['vehicle_active_date2'],
					"auto_last_lat"        => substr($jsonautocheck->auto_last_lat, 0, 10),
					"auto_last_long"       => substr($jsonautocheck->auto_last_long, 0, 10),
					"auto_last_road"       => $jsonautocheck->auto_last_road,
					"auto_last_engine"     => $jsonautocheck->auto_last_engine,
					"auto_last_speed"      => $jsonautocheck->auto_last_speed,
					"auto_last_course"     => $jsonautocheck->auto_last_course,
				));
			}
	}

	// echo "<pre>";
	// var_dump(sizeof($datafix));die();
	// echo "<pre>";

	echo json_encode(array("code" => "success", "msg" => "success", "data" => $datafix, "alldataforclearmaps" => $forclearmaps));
}

function getlistengineon(){
	$allCompany 					 = $this->m_poipoolmaster->getAllCompany();
	$mastervehicle         = $this->m_poipoolmaster->getmastervehicle();
	$datafix               = array();

	$dataContractor['BKAE'] = 0;
	$dataContractor['KMB']  = 0;
	$dataContractor['GECL'] = 0;
	$dataContractor['STLI'] = 0;
	$dataContractor['RAMB'] = 0;
	$dataContractor['BBS']  = 0;
	$dataContractor['MKS']  = 0;
	$dataContractor['RBT']  = 0;
	$dataContractor['MMS']  = 0;
	$dataContractor['EST']  = 0;

	$lasttimecheck          = json_decode($mastervehicle[0]['vehicle_autocheck']);

	for ($i=0; $i < sizeof($mastervehicle); $i++) {
		$autocheck = json_decode($mastervehicle[$i]['vehicle_autocheck']);

		if ($autocheck->auto_last_engine == "ON") {
			array_push($datafix, array(
				"vehicle_no"       => $mastervehicle[$i]['vehicle_no'],
				"vehicle_name"     => $mastervehicle[$i]['vehicle_name'],
				"vehicle_company"  => $mastervehicle[$i]['vehicle_company'],
				"auto_last_lat"    => $autocheck->auto_last_lat,
				"auto_last_long"   => $autocheck->auto_last_long,
				"auto_last_engine" => $autocheck->auto_last_engine,
				"auto_last_speed"  => $autocheck->auto_last_speed,
				"auto_last_update" => date("d-m-Y H:i:s", strtotime($autocheck->auto_last_update))
			));
		}
	}

	for ($k=0; $k < sizeof($datafix); $k++) {
		for ($j=0; $j < sizeof($allCompany); $j++) {
			if ($datafix[$k]['vehicle_company'] == $allCompany[$j]['company_id']) {
				if ($allCompany[$j]['company_name'] == "BKAE") {
					$dataContractor['BKAE'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "KMB") {
					$dataContractor['KMB'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "GECL") {
					$dataContractor['GECL'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "STLI") {
					$dataContractor['STLI'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "RAMB") {
					$dataContractor['RAMB'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "BBS") {
					$dataContractor['BBS'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "MKS") {
					$dataContractor['MKS'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "RBT") {
					$dataContractor['RBT'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "MMS") {
					$dataContractor['MMS'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "EST") {
					$dataContractor['EST'] += 1;
				}
			}
		}
	}

	echo json_encode(array("msg" => "success", "code" => 200, "allCompany" => $allCompany, "jumlah_contractor" => $dataContractor, "data" => $datafix, "lastcheck" => date("d-m-Y H:i:s", strtotime($lasttimecheck->auto_last_update))));
}

function getlistengineoff(){
	$allCompany 					 = $this->m_poipoolmaster->getAllCompany();
	$mastervehicle         = $this->m_poipoolmaster->getmastervehicle();
	$datafix               = array();

	$dataContractor['BKAE'] = 0;
	$dataContractor['KMB']  = 0;
	$dataContractor['GECL'] = 0;
	$dataContractor['STLI'] = 0;
	$dataContractor['RAMB'] = 0;
	$dataContractor['BBS']  = 0;
	$dataContractor['MKS']  = 0;
	$dataContractor['RBT']  = 0;
	$dataContractor['MMS']  = 0;
	$dataContractor['EST']  = 0;

	$lasttimecheck          = json_decode($mastervehicle[0]['vehicle_autocheck']);

	for ($i=0; $i < sizeof($mastervehicle); $i++) {
		$autocheck = json_decode($mastervehicle[$i]['vehicle_autocheck']);

		if ($autocheck->auto_last_engine == "OFF") {
			$lastspeed = $autocheck->auto_last_speed;

				if ($lastspeed > 0) {
					$lastspeed = 0;
				}else {
					$lastspeed = $autocheck->auto_last_speed;
				}
			array_push($datafix, array(
				"vehicle_no"       => $mastervehicle[$i]['vehicle_no'],
				"vehicle_name"     => $mastervehicle[$i]['vehicle_name'],
				"vehicle_company"  => $mastervehicle[$i]['vehicle_company'],
				"auto_last_lat"    => $autocheck->auto_last_lat,
				"auto_last_long"   => $autocheck->auto_last_long,
				"auto_last_engine" => $autocheck->auto_last_engine,
				"auto_last_speed"  => $lastspeed,
				"auto_last_update" => date("d-m-Y H:i:s", strtotime($autocheck->auto_last_update))
			));
		}
	}

	for ($k=0; $k < sizeof($datafix); $k++) {
		for ($j=0; $j < sizeof($allCompany); $j++) {
			if ($datafix[$k]['vehicle_company'] == $allCompany[$j]['company_id']) {
				if ($allCompany[$j]['company_name'] == "BKAE") {
					$dataContractor['BKAE'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "KMB") {
					$dataContractor['KMB'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "GECL") {
					$dataContractor['GECL'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "STLI") {
					$dataContractor['STLI'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "RAMB") {
					$dataContractor['RAMB'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "BBS") {
					$dataContractor['BBS'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "MKS") {
					$dataContractor['MKS'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "RBT") {
					$dataContractor['RBT'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "MMS") {
					$dataContractor['MMS'] += 1;
				}elseif ($allCompany[$j]['company_name'] == "EST") {
					$dataContractor['EST'] += 1;
				}
			}
		}
	}

	echo json_encode(array("msg" => "success", "code" => 200, "allCompany" => $allCompany, "jumlah_contractor" => $dataContractor, "data" => $datafix, "lastcheck" => date("d-m-Y H:i:s", strtotime($lasttimecheck->auto_last_update))));
}

function getstreetautomatic(){
	$typeofstreet 		 = $_POST['typeofstreet'];
	$masterdatavehicle = $this->m_poipoolmaster->getmastervehicle();
	$allStreet         = $this->m_poipoolmaster->getstreet_now($typeofstreet);
	$dataRomFix        = array();
	$lasttimecheck 		 = date("d-m-Y H:i:s", strtotime("+1 hour"));

	for ($j=0; $j < sizeof($allStreet); $j++) {
		$street_name                = explode(",", $allStreet[$j]['street_alias']);
		$street_namefix             = $street_name[0];
		$dataState[$street_namefix] = 0;

		for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
			$autocheck          = json_decode($masterdatavehicle[$i]['vehicle_autocheck']);
			$auto_last_position = explode(",", $autocheck->auto_last_position);
			$datalastposition   = $auto_last_position[0];
			$auto_status 		    = $autocheck->auto_status;

			if ($auto_status != "M") {
				if ($datalastposition == $street_namefix) {
						$dataState[$street_namefix] += 1;
				}
			}
		}
	}

	// echo "<pre>";
	// var_dump($dataState);die();
	// echo "<pre>";

	echo json_encode(array("msg" => "success", "code" => 200, "data" => $dataState, "allstreet" => $allStreet, "lastcheck" => $lasttimecheck));
}

function getstreetautomaticbycompanyid(){
	$typeofstreet 		 = $_POST['typeofstreet'];
	$companyid 		 	   = $_POST['companyid'];
	$masterdatavehicle = $this->m_poipoolmaster->getmastervehiclebycontractor($companyid);
	$allStreet         = $this->m_poipoolmaster->getstreet_now($typeofstreet);
	$dataRomFix        = array();
	$lasttimecheck 		 = date("d-m-Y H:i:s", strtotime("+1 hour"));

	// echo "<pre>";
	// var_dump($allStreet);die();
	// echo "<pre>";

	for ($j=0; $j < sizeof($allStreet); $j++) {
		$street_name                = explode(",", $allStreet[$j]['street_name']);
		$street_namefix             = $street_name[0];
		$dataState[$street_namefix] = 0;

		for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
			$autocheck          = json_decode($masterdatavehicle[$i]['vehicle_autocheck']);
			$auto_last_position = explode(",", $autocheck->auto_last_position);
			$datalastposition   = $auto_last_position[0];
			$auto_status 		    = $autocheck->auto_status;

			if ($auto_status != "M") {
				if ($datalastposition == $street_namefix) {
						$dataState[$street_namefix] += 1;
				}
			}
		}
	}

	// echo "<pre>";
	// var_dump($dataState);die();
	// echo "<pre>";

	// LIMIT SETTING PER KM
	$getdataFromStreet = $this->m_poipoolmaster->getstreet_now($typeofstreet);
	$mapSettingType    = $this->m_poipoolmaster->getMapSettingByType($typeofstreet);

	$postfix_middle_limit = "_middle_limit";
	$postfix_top_limit    = "_top_limit";

	if (isset($getdataFromStreet)) {
		$datafixlimitperkm = array();
		for ($i=0; $i < sizeof($getdataFromStreet); $i++) {
			$streetremovecoma         = explode(",", $getdataFromStreet[$i]['street_name']);
			$streetfix                = str_replace(" ", "_", $streetremovecoma[0]);

			$middlelimitname          = $streetfix.$postfix_middle_limit;
			$toplimitname             = $streetfix.$postfix_top_limit;

			$getMapSettingByLimitName = $this->m_poipoolmaster->getThisMapSettingByLimitName($middlelimitname, $toplimitname);

			if (sizeof($getMapSettingByLimitName) > 1) {
					array_push($datafixlimitperkm, array(
						"street_id"               => $getdataFromStreet[$i]['street_id'],
						"street_name"             => $getdataFromStreet[$i]['street_name'],
						"mapsetting_type"         => 1,
						"mapsetting_name_alias"   => $streetremovecoma[0],
						"mapsetting_name"         => $streetfix,
						"mapsetting_middle_limit" => $getMapSettingByLimitName[0]['mapsetting_limit_value'],
						"mapsetting_top_limit"    => $getMapSettingByLimitName[1]['mapsetting_limit_value']
					));
			}else {
				array_push($datafixlimitperkm, array(
					"street_id"               => $getdataFromStreet[$i]['street_id'],
					"street_name"             => $getdataFromStreet[$i]['street_name'],
					"mapsetting_type"         => 1,
					"mapsetting_name_alias"   => $streetremovecoma[0],
					"mapsetting_name"         => $streetfix,
					"mapsetting_middle_limit" => 0,
					"mapsetting_top_limit"    => 0
				));
			}
		}
	}

	// echo "<pre>";
	// var_dump($dataState);die();
	// echo "<pre>";

	echo json_encode(array("msg" => "success", "code" => 200, "data" => $dataState, "allstreet" => $allStreet, "lastcheck" => $lasttimecheck, "datafixlimit" => $datafixlimitperkm));
}

function getpoolnew(){
	$companyid 		 	   = $_POST['companyid'];
	$getallcompany     = $this->m_poipoolmaster->getAllCompany();
	$masterdatavehicle = $this->m_poipoolmaster->getmastervehiclebycontractor($companyid);
	$lasttimecheck 		 = date("d-m-Y H:i:s", strtotime("+1 hour"));
	$dataoutofhauling['outofhauling'] = 0;


	$datapool  = array();
	for ($i=0; $i < sizeof($getallcompany); $i++) {
		$getChild      = $this->m_poipoolmaster->getStreetByParent($getallcompany[$i]['company_id']);
			for ($j=0; $j < sizeof($getChild); $j++) {
				$child_name = explode(",", $getChild[$j]['street_name']);
				array_push($datapool, array(
					$getallcompany[$i]['company_name'] => $getallcompany[$i]['company_name'].'|'.$child_name[0]
				));
			}
	}

	for ($i=0; $i < sizeof($getallcompany); $i++) {
		$datacompany                = $getallcompany[$i]['company_name'];
		$street_namefix             = $datacompany;
		$dataState[$street_namefix] = 0;

			for ($j=0; $j < sizeof($datapool); $j++) {
				if (isset($datapool[$j][$datacompany])) {
					$datachild = explode("|", $datapool[$j][$datacompany]);

					if ($datacompany == $datachild[0]) {
							for ($k=0; $k < sizeof($masterdatavehicle); $k++) {
									$autocheck          = json_decode($masterdatavehicle[$k]['vehicle_autocheck']);
									$auto_last_position = explode(",", $autocheck->auto_last_position);
									$datalastposition   = $auto_last_position[0];
									$auto_status 		    = $autocheck->auto_status;

									if ($auto_status != "M" && $autocheck->auto_last_speed < 1) {
										if ($datalastposition == $datachild[1]) {
											$dataState[$street_namefix] += 1;
										}
									}
								}
					}
				}
			}
	}

	for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
		$autocheck         = json_decode($masterdatavehicle[$i]['vehicle_autocheck']);
		$auto_last_hauling = explode(",", $autocheck->auto_last_hauling);
		$datalastposition  = $auto_last_hauling[0];

			if ($datalastposition == "out") {
				$dataoutofhauling['outofhauling'] += 1;
			}
	}

	// echo "<pre>";
	// var_dump($dataoutofhauling);die();
	// echo "<pre>";

	echo json_encode(array("msg" => "success", "code" => 200, "data" => $dataState, "allcompany" => $getallcompany, "dataoutofhauling" => $dataoutofhauling, "lastcheck" => $lasttimecheck));
}

function getChildPool(){
	$poolparent 		 	                = $_POST['poolparent'];
	$contractor 		 	                = $_POST['contractor'];
	$masterdatavehicle                = $this->m_poipoolmaster->getmastervehiclebycontractor($contractor);
	$masterdatavehiclebycontractor    = $this->m_poipoolmaster->getmastervehiclebycontractor($poolparent);
	$allStreet                        = $this->m_poipoolmaster->getstreet_now_byparent($poolparent);
	$lasttimecheck 		                = date("d-m-Y H:i:s", strtotime("+1 hour"));
	$dataoutofhauling['outofhauling'] = 0;

	// echo "<pre>";
	// var_dump($poolparent.'-'.$contractor);die();
	// echo "<pre>";


	for ($j=0; $j < sizeof($allStreet); $j++) {
		$street_name                = explode(",", $allStreet[$j]['street_name']);
		$street_namefix             = $street_name[0];
		$dataState[$street_namefix] = 0;

		for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
			$autocheck          = json_decode($masterdatavehicle[$i]['vehicle_autocheck']);
			$auto_last_position = explode(",", $autocheck->auto_last_position);
			$datalastposition   = $auto_last_position[0];
			$auto_status 		    = $autocheck->auto_status;

			if ($auto_status != "M" && $autocheck->auto_last_speed < 1) {
				if ($datalastposition == $street_namefix) {
						$dataState[$street_namefix] += 1;
				}
			}
		}
	}

	// DATA OUT OF OTHERS -> OUT OF HAULING
	for ($i=0; $i < sizeof($masterdatavehiclebycontractor); $i++) {
		$autocheck         = json_decode($masterdatavehiclebycontractor[$i]['vehicle_autocheck']);
		$auto_last_hauling = explode(",", $autocheck->auto_last_hauling);
		$datalastposition  = $auto_last_hauling[0];

			if ($datalastposition == "out") {
				$dataoutofhauling['outofhauling'] += 1;
			}
	}

	// echo "<pre>";
	// var_dump($dataState);die();
	// echo "<pre>";
	echo json_encode(array("msg" => "success", "code" => 200, "data" => $dataState, "allStreet" => $allStreet, "dataoutofhauling" => $dataoutofhauling, "lastcheck" => $lasttimecheck));
}

function getPoolOther(){
	$companyid 		 	                  = $_POST['companyid'];
	$dataforclear                     = $this->m_poipoolmaster->getmastervehicle();
	$masterdatavehicle                = $this->m_poipoolmaster->getmastervehiclebycontractor($companyid);
	$poolmaster                       = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");
	$dataoutofhauling['outofhauling'] = 0;
	$dataVehicleOutofHauling          = array();
	$lasttimecheck 		                = date("d-m-Y H:i:s", strtotime("+1 hour"));

		for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
			$autocheck         = json_decode($masterdatavehicle[$i]['vehicle_autocheck']);
			$auto_last_hauling = explode(",", $autocheck->auto_last_hauling);
			$datalastposition  = $auto_last_hauling[0];

				if ($datalastposition == "out") {
					$dataoutofhauling['outofhauling'] += 1;

					array_push($dataVehicleOutofHauling, array(
						"vehicle_id"       => $masterdatavehicle[$i]['vehicle_id'],
						"vehicle_no"       => $masterdatavehicle[$i]['vehicle_no'],
						"vehicle_name"     => $masterdatavehicle[$i]['vehicle_name'],
						"vehicle_device"   => $masterdatavehicle[$i]['vehicle_device'],
						"vehicle_company"  => $masterdatavehicle[$i]['vehicle_company'],
						"auto_last_lat"    => $autocheck->auto_last_lat,
						"auto_last_long"   => $autocheck->auto_last_long,
						"auto_last_course" => $autocheck->auto_last_course,
					));
				}
		}

		$getallcompany     = $this->m_poipoolmaster->getAllCompany();

		$datapool  = array();
		for ($i=0; $i < sizeof($getallcompany); $i++) {
			$getChild      = $this->m_poipoolmaster->getStreetByParent($getallcompany[$i]['company_id']);
				for ($j=0; $j < sizeof($getChild); $j++) {
					$child_name = explode(",", $getChild[$j]['street_name']);
					array_push($datapool, array(
						$getallcompany[$i]['company_name'] => $getallcompany[$i]['company_name'].'|'.$child_name[0]
					));
				}
		}

		for ($i=0; $i < sizeof($getallcompany); $i++) {
			$datacompanyid              = $getallcompany[$i]['company_id'];
			$datacompanyname            = $getallcompany[$i]['company_name'];
			$street_namefix             = $datacompanyname;
			$dataState[$street_namefix] = 0;

			for ($k=0; $k < sizeof($dataVehicleOutofHauling); $k++) {
				$vehicle_company = $dataVehicleOutofHauling[$k]['vehicle_company'];
					if ($datacompanyid == $vehicle_company) {
						$dataState[$street_namefix] += 1;
					}
			}
		}

		// echo "<pre>";
		// var_dump($getallcompany);die();
		// echo "<pre>";

		echo json_encode(array("msg" => "success", "code" => 200, "data" => $dataState, "company" => $getallcompany));
}

//FUNCTION KHUSUS SINGLE URL START
	function singlerom(){
		ini_set('max_execution_time', '300');
		set_time_limit(300);
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$user_id       = $this->sess->user_id;
		$user_parent   = $this->sess->user_parent;
		$privilegecode = $this->sess->user_id_role;
		$user_company  = $this->sess->user_company;

		if($privilegecode == 0){
			$user_id_fix = $user_id;
		}elseif ($privilegecode == 1) {
			$user_id_fix = $user_parent;
		}elseif ($privilegecode == 2) {
			$user_id_fix = $user_parent;
		}elseif ($privilegecode == 3) {
			$user_id_fix = $user_parent;
		}elseif ($privilegecode == 4) {
			$user_id_fix = $user_parent;
		}elseif ($privilegecode == 5) {
			$user_id_fix = $user_id;
		}elseif ($privilegecode == 6) {
			$user_id_fix = $user_id;
		}else{
			$user_id_fix = $user_id;
		}

		$companyid                       = $this->sess->user_company;
		$user_dblive                     = $this->sess->user_dblive;
		$mastervehicle                   = $this->m_poipoolmaster->getmastervehicleforheatmap();

		$datafix                         = array();
		$deviceidygtidakada              = array();
		$statusvehicle['engine_on']  = 0;
		$statusvehicle['engine_off'] = 0;

		for ($i=0; $i < sizeof($mastervehicle); $i++) {
			$jsonautocheck = json_decode($mastervehicle[$i]['vehicle_autocheck']);
			if (isset($jsonautocheck->auto_status)) {
				// code...
			$auto_status   = $jsonautocheck->auto_status;

			if ($privilegecode == 5 || $privilegecode == 6) {
				if ($mastervehicle[$i]['vehicle_company'] == $user_company) {
					if ($jsonautocheck->auto_last_engine == "ON") {
						$statusvehicle['engine_on'] += 1;
					}else {
						$statusvehicle['engine_off'] += 1;
					}
				}
			}else {
				if ($jsonautocheck->auto_last_engine == "ON") {
					$statusvehicle['engine_on'] += 1;
				}else {
					$statusvehicle['engine_off'] += 1;
				}
			}

				if ($auto_status != "M") {
					array_push($datafix, array(
						"vehicle_id"             => $mastervehicle[$i]['vehicle_id'],
						"vehicle_user_id"        => $mastervehicle[$i]['vehicle_user_id'],
						"vehicle_company"        => $mastervehicle[$i]['vehicle_company'],
						"vehicle_device"         => $mastervehicle[$i]['vehicle_device'],
						"vehicle_no"             => $mastervehicle[$i]['vehicle_no'],
						"vehicle_name"           => $mastervehicle[$i]['vehicle_name'],
						"vehicle_active_date2"   => $mastervehicle[$i]['vehicle_active_date2'],
						"auto_last_lat"          => substr($jsonautocheck->auto_last_lat, 0, 10),
						"auto_last_long"         => substr($jsonautocheck->auto_last_long, 0, 10),
					));
				}
			}
		}

		$company                  = $this->dashboardmodel->getcompany_byowner($privilegecode);
			if ($company) {

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
			}else {
				$this->params['company']   = 0;
				$this->params['companyid'] = 0;
				$this->params['vehicle']   = 0;
			}

		// echo "<pre>";
		// var_dump($company);die();
		// echo "<pre>";


		$this->params['url_code_view']  = "1";
		$this->params['code_view_menu'] = "monitor";
		$this->params['maps_code']      = "morehundred";

		$this->params['engine_on']      = $statusvehicle['engine_on'];
		$this->params['engine_off']     = $statusvehicle['engine_off'];


		$rstatus                        = $this->dashboardmodel->gettotalstatus($this->sess->user_id);

		$datastatus                     = explode("|", $rstatus);
		$this->params['total_online']   = $datastatus[0]+$datastatus[1]; //p + K
		$this->params['total_vehicle']  = $datastatus[3];
		$this->params['total_offline']  = $datastatus[2];

		$this->params['vehicledata']  = $datafix;
		$this->params['vehicletotal'] = sizeof($mastervehicle);
		$this->params['poolmaster']   = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");
		$getvehicle_byowner           = $this->dashboardmodel->getvehicle_byownerforheatmap();
		// echo "<pre>";
		// var_dump($getvehicle_byowner);die();
		// echo "<pre>";
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
		$this->params['mapsetting']     = $this->m_poipoolmaster->getmapsetting();
		$this->params['poolmaster']     = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");

		// echo "<pre>";
		// var_dump($this->params['mapsetting']);die();
		// echo "<pre>";

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

			if ($privilegecode == 1) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/singleurl/v_home_rom', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
			}elseif ($privilegecode == 2) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/singleurl/v_home_rom', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
			}elseif ($privilegecode == 3) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/singleurl/v_home_rom', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
			}elseif ($privilegecode == 4) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/singleurl/v_home_rom', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
			}elseif ($privilegecode == 5) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/singleurl/v_home_rom', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
			}elseif ($privilegecode == 6) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/singleurl/v_home_rom', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
			}else {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/singleurl/v_home_rom', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
			}
	}

	function singlequickcount(){
		ini_set('max_execution_time', '300');
		set_time_limit(300);
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$user_id       = $this->sess->user_id;
		$user_parent   = $this->sess->user_parent;
		$privilegecode = $this->sess->user_id_role;
		$user_company  = $this->sess->user_company;

		if($privilegecode == 0){
			$user_id_fix = $user_id;
		}elseif ($privilegecode == 1) {
			$user_id_fix = $user_parent;
		}elseif ($privilegecode == 2) {
			$user_id_fix = $user_parent;
		}elseif ($privilegecode == 3) {
			$user_id_fix = $user_parent;
		}elseif ($privilegecode == 4) {
			$user_id_fix = $user_parent;
		}elseif ($privilegecode == 5) {
			$user_id_fix = $user_id;
		}elseif ($privilegecode == 6) {
			$user_id_fix = $user_id;
		}else{
			$user_id_fix = $user_id;
		}

		$companyid                       = $this->sess->user_company;
		$user_dblive                     = $this->sess->user_dblive;
		$mastervehicle                   = $this->m_poipoolmaster->getmastervehicleforheatmap();

		$datafix                         = array();
		$deviceidygtidakada              = array();
		$statusvehicle['engine_on']  = 0;
		$statusvehicle['engine_off'] = 0;

		for ($i=0; $i < sizeof($mastervehicle); $i++) {
			$jsonautocheck = json_decode($mastervehicle[$i]['vehicle_autocheck']);
			if (isset($jsonautocheck->auto_status)) {
				// code...
			$auto_status   = $jsonautocheck->auto_status;

			if ($privilegecode == 5 || $privilegecode == 6) {
				if ($mastervehicle[$i]['vehicle_company'] == $user_company) {
					if ($jsonautocheck->auto_last_engine == "ON") {
						$statusvehicle['engine_on'] += 1;
					}else {
						$statusvehicle['engine_off'] += 1;
					}
				}
			}else {
				if ($jsonautocheck->auto_last_engine == "ON") {
					$statusvehicle['engine_on'] += 1;
				}else {
					$statusvehicle['engine_off'] += 1;
				}
			}

				if ($auto_status != "M") {
					array_push($datafix, array(
						"vehicle_id"             => $mastervehicle[$i]['vehicle_id'],
						"vehicle_user_id"        => $mastervehicle[$i]['vehicle_user_id'],
						"vehicle_company"        => $mastervehicle[$i]['vehicle_company'],
						"vehicle_device"         => $mastervehicle[$i]['vehicle_device'],
						"vehicle_no"             => $mastervehicle[$i]['vehicle_no'],
						"vehicle_name"           => $mastervehicle[$i]['vehicle_name'],
						"vehicle_active_date2"   => $mastervehicle[$i]['vehicle_active_date2'],
						"auto_last_lat"          => substr($jsonautocheck->auto_last_lat, 0, 10),
						"auto_last_long"         => substr($jsonautocheck->auto_last_long, 0, 10),
					));
				}
			}
		}

		$company                  = $this->dashboardmodel->getcompany_byowner($privilegecode);
			if ($company) {

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
			}else {
				$this->params['company']   = 0;
				$this->params['companyid'] = 0;
				$this->params['vehicle']   = 0;
			}

		// echo "<pre>";
		// var_dump($company);die();
		// echo "<pre>";


		$this->params['url_code_view']  = "1";
		$this->params['code_view_menu'] = "monitor";
		$this->params['maps_code']      = "morehundred";

		$this->params['engine_on']      = $statusvehicle['engine_on'];
		$this->params['engine_off']     = $statusvehicle['engine_off'];


		$rstatus                        = $this->dashboardmodel->gettotalstatus($this->sess->user_id);

		$datastatus                     = explode("|", $rstatus);
		$this->params['total_online']   = $datastatus[0]+$datastatus[1]; //p + K
		$this->params['total_vehicle']  = $datastatus[3];
		$this->params['total_offline']  = $datastatus[2];

		$this->params['vehicledata']  = $datafix;
		$this->params['vehicletotal'] = sizeof($mastervehicle);
		$this->params['poolmaster']   = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");
		$getvehicle_byowner           = $this->dashboardmodel->getvehicle_byownerforheatmap();
		// echo "<pre>";
		// var_dump($getvehicle_byowner);die();
		// echo "<pre>";
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
		$this->params['mapsetting']     = $this->m_poipoolmaster->getmapsetting();
		$this->params['poolmaster']     = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");

		// echo "<pre>";
		// var_dump($this->params['mapsetting']);die();
		// echo "<pre>";

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

			if ($privilegecode == 1) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/singleurl/v_home_quickcount', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
			}elseif ($privilegecode == 2) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/singleurl/v_home_quickcount', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
			}elseif ($privilegecode == 3) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/singleurl/v_home_quickcount', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
			}elseif ($privilegecode == 4) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/singleurl/v_home_quickcount', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
			}elseif ($privilegecode == 5) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/singleurl/v_home_quickcount', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
			}elseif ($privilegecode == 6) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/singleurl/v_home_quickcount', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
			}else {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/singleurl/v_home_quickcount', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
			}
	}


	function singleport(){
		ini_set('max_execution_time', '300');
		set_time_limit(300);
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$user_id       = $this->sess->user_id;
		$user_parent   = $this->sess->user_parent;
		$privilegecode = $this->sess->user_id_role;
		$user_company  = $this->sess->user_company;

		if($privilegecode == 0){
			$user_id_fix = $user_id;
		}elseif ($privilegecode == 1) {
			$user_id_fix = $user_parent;
		}elseif ($privilegecode == 2) {
			$user_id_fix = $user_parent;
		}elseif ($privilegecode == 3) {
			$user_id_fix = $user_parent;
		}elseif ($privilegecode == 4) {
			$user_id_fix = $user_parent;
		}elseif ($privilegecode == 5) {
			$user_id_fix = $user_id;
		}elseif ($privilegecode == 6) {
			$user_id_fix = $user_id;
		}else{
			$user_id_fix = $user_id;
		}

		$companyid                       = $this->sess->user_company;
		$user_dblive                     = $this->sess->user_dblive;
		$mastervehicle                   = $this->m_poipoolmaster->getmastervehicleforheatmap();

		$datafix                         = array();
		$deviceidygtidakada              = array();
		$statusvehicle['engine_on']  = 0;
		$statusvehicle['engine_off'] = 0;

		for ($i=0; $i < sizeof($mastervehicle); $i++) {
			$jsonautocheck = json_decode($mastervehicle[$i]['vehicle_autocheck']);
			if (isset($jsonautocheck->auto_status)) {
				// code...
			$auto_status   = $jsonautocheck->auto_status;

			if ($privilegecode == 5 || $privilegecode == 6) {
				if ($mastervehicle[$i]['vehicle_company'] == $user_company) {
					if ($jsonautocheck->auto_last_engine == "ON") {
						$statusvehicle['engine_on'] += 1;
					}else {
						$statusvehicle['engine_off'] += 1;
					}
				}
			}else {
				if ($jsonautocheck->auto_last_engine == "ON") {
					$statusvehicle['engine_on'] += 1;
				}else {
					$statusvehicle['engine_off'] += 1;
				}
			}

				if ($auto_status != "M") {
					array_push($datafix, array(
						"vehicle_id"             => $mastervehicle[$i]['vehicle_id'],
						"vehicle_user_id"        => $mastervehicle[$i]['vehicle_user_id'],
						"vehicle_company"        => $mastervehicle[$i]['vehicle_company'],
						"vehicle_device"         => $mastervehicle[$i]['vehicle_device'],
						"vehicle_no"             => $mastervehicle[$i]['vehicle_no'],
						"vehicle_name"           => $mastervehicle[$i]['vehicle_name'],
						"vehicle_active_date2"   => $mastervehicle[$i]['vehicle_active_date2'],
						"auto_last_lat"          => substr($jsonautocheck->auto_last_lat, 0, 10),
						"auto_last_long"         => substr($jsonautocheck->auto_last_long, 0, 10),
					));
				}
			}
		}

		$company                  = $this->dashboardmodel->getcompany_byowner($privilegecode);
			if ($company) {

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
			}else {
				$this->params['company']   = 0;
				$this->params['companyid'] = 0;
				$this->params['vehicle']   = 0;
			}

		// echo "<pre>";
		// var_dump($company);die();
		// echo "<pre>";


		$this->params['url_code_view']  = "1";
		$this->params['code_view_menu'] = "monitor";
		$this->params['maps_code']      = "morehundred";

		$this->params['engine_on']      = $statusvehicle['engine_on'];
		$this->params['engine_off']     = $statusvehicle['engine_off'];


		$rstatus                        = $this->dashboardmodel->gettotalstatus($this->sess->user_id);

		$datastatus                     = explode("|", $rstatus);
		$this->params['total_online']   = $datastatus[0]+$datastatus[1]; //p + K
		$this->params['total_vehicle']  = $datastatus[3];
		$this->params['total_offline']  = $datastatus[2];

		$this->params['vehicledata']  = $datafix;
		$this->params['vehicletotal'] = sizeof($mastervehicle);
		$this->params['poolmaster']   = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");
		$getvehicle_byowner           = $this->dashboardmodel->getvehicle_byownerforheatmap();
		// echo "<pre>";
		// var_dump($getvehicle_byowner);die();
		// echo "<pre>";
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
		$this->params['mapsetting']     = $this->m_poipoolmaster->getmapsetting();
		$this->params['poolmaster']     = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");

		// echo "<pre>";
		// var_dump($this->params['mapsetting']);die();
		// echo "<pre>";

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

			if ($privilegecode == 1) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/singleurl/v_home_port', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
			}elseif ($privilegecode == 2) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/singleurl/v_home_port', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
			}elseif ($privilegecode == 3) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/singleurl/v_home_port', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
			}elseif ($privilegecode == 4) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/singleurl/v_home_port', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
			}elseif ($privilegecode == 5) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/singleurl/v_home_port', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
			}elseif ($privilegecode == 6) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/singleurl/v_home_port', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
			}else {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/singleurl/v_home_port', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
			}
	}

	function singlepool(){
		ini_set('max_execution_time', '300');
		set_time_limit(300);
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$user_id       = $this->sess->user_id;
		$user_parent   = $this->sess->user_parent;
		$privilegecode = $this->sess->user_id_role;
		$user_company  = $this->sess->user_company;

		if($privilegecode == 0){
			$user_id_fix = $user_id;
		}elseif ($privilegecode == 1) {
			$user_id_fix = $user_parent;
		}elseif ($privilegecode == 2) {
			$user_id_fix = $user_parent;
		}elseif ($privilegecode == 3) {
			$user_id_fix = $user_parent;
		}elseif ($privilegecode == 4) {
			$user_id_fix = $user_parent;
		}elseif ($privilegecode == 5) {
			$user_id_fix = $user_id;
		}elseif ($privilegecode == 6) {
			$user_id_fix = $user_id;
		}else{
			$user_id_fix = $user_id;
		}

		$companyid                       = $this->sess->user_company;
		$user_dblive                     = $this->sess->user_dblive;
		$mastervehicle                   = $this->m_poipoolmaster->getmastervehicleforheatmap();

		$datafix                         = array();
		$deviceidygtidakada              = array();
		$statusvehicle['engine_on']  = 0;
		$statusvehicle['engine_off'] = 0;

		for ($i=0; $i < sizeof($mastervehicle); $i++) {
			$jsonautocheck = json_decode($mastervehicle[$i]['vehicle_autocheck']);
			if (isset($jsonautocheck->auto_status)) {
				// code...
			$auto_status   = $jsonautocheck->auto_status;

			if ($privilegecode == 5 || $privilegecode == 6) {
				if ($mastervehicle[$i]['vehicle_company'] == $user_company) {
					if ($jsonautocheck->auto_last_engine == "ON") {
						$statusvehicle['engine_on'] += 1;
					}else {
						$statusvehicle['engine_off'] += 1;
					}
				}
			}else {
				if ($jsonautocheck->auto_last_engine == "ON") {
					$statusvehicle['engine_on'] += 1;
				}else {
					$statusvehicle['engine_off'] += 1;
				}
			}

				if ($auto_status != "M") {
					array_push($datafix, array(
						"vehicle_id"             => $mastervehicle[$i]['vehicle_id'],
						"vehicle_user_id"        => $mastervehicle[$i]['vehicle_user_id'],
						"vehicle_company"        => $mastervehicle[$i]['vehicle_company'],
						"vehicle_device"         => $mastervehicle[$i]['vehicle_device'],
						"vehicle_no"             => $mastervehicle[$i]['vehicle_no'],
						"vehicle_name"           => $mastervehicle[$i]['vehicle_name'],
						"vehicle_active_date2"   => $mastervehicle[$i]['vehicle_active_date2'],
						"auto_last_lat"          => substr($jsonautocheck->auto_last_lat, 0, 10),
						"auto_last_long"         => substr($jsonautocheck->auto_last_long, 0, 10),
					));
				}
			}
		}

		$company                  = $this->dashboardmodel->getcompany_byowner($privilegecode);
			if ($company) {

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
			}else {
				$this->params['company']   = 0;
				$this->params['companyid'] = 0;
				$this->params['vehicle']   = 0;
			}

		// echo "<pre>";
		// var_dump($company);die();
		// echo "<pre>";


		$this->params['url_code_view']  = "1";
		$this->params['code_view_menu'] = "monitor";
		$this->params['maps_code']      = "morehundred";

		$this->params['engine_on']      = $statusvehicle['engine_on'];
		$this->params['engine_off']     = $statusvehicle['engine_off'];


		$rstatus                        = $this->dashboardmodel->gettotalstatus($this->sess->user_id);

		$datastatus                     = explode("|", $rstatus);
		$this->params['total_online']   = $datastatus[0]+$datastatus[1]; //p + K
		$this->params['total_vehicle']  = $datastatus[3];
		$this->params['total_offline']  = $datastatus[2];

		$this->params['vehicledata']  = $datafix;
		$this->params['vehicletotal'] = sizeof($mastervehicle);
		$this->params['poolmaster']   = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");
		$getvehicle_byowner           = $this->dashboardmodel->getvehicle_byownerforheatmap();
		// echo "<pre>";
		// var_dump($getvehicle_byowner);die();
		// echo "<pre>";
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
		$this->params['mapsetting']     = $this->m_poipoolmaster->getmapsetting();
		$this->params['poolmaster']     = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");

		// echo "<pre>";
		// var_dump($this->params['mapsetting']);die();
		// echo "<pre>";

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

			if ($privilegecode == 1) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/singleurl/v_home_pool', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
			}elseif ($privilegecode == 2) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/singleurl/v_home_pool', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
			}elseif ($privilegecode == 3) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/singleurl/v_home_pool', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
			}elseif ($privilegecode == 4) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/singleurl/v_home_pool', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
			}elseif ($privilegecode == 5) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/singleurl/v_home_pool', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
			}elseif ($privilegecode == 6) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/singleurl/v_home_pool', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
			}else {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/singleurl/v_home_pool', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
			}
	}

	function singleheatmap(){
		ini_set('max_execution_time', '300');
		set_time_limit(300);
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$user_id       = $this->sess->user_id;
		$user_parent   = $this->sess->user_parent;
		$privilegecode = $this->sess->user_id_role;
		$user_company  = $this->sess->user_company;

		if($privilegecode == 0){
			$user_id_fix = $user_id;
		}elseif ($privilegecode == 1) {
			$user_id_fix = $user_parent;
		}elseif ($privilegecode == 2) {
			$user_id_fix = $user_parent;
		}elseif ($privilegecode == 3) {
			$user_id_fix = $user_parent;
		}elseif ($privilegecode == 4) {
			$user_id_fix = $user_parent;
		}elseif ($privilegecode == 5) {
			$user_id_fix = $user_id;
		}elseif ($privilegecode == 6) {
			$user_id_fix = $user_id;
		}else{
			$user_id_fix = $user_id;
		}

		$companyid                       = $this->sess->user_company;
		$user_dblive                     = $this->sess->user_dblive;
		$mastervehicle                   = $this->m_poipoolmaster->getmastervehicleforheatmap();

		$datafix                         = array();
		$deviceidygtidakada              = array();
		$statusvehicle['engine_on']  = 0;
		$statusvehicle['engine_off'] = 0;

		for ($i=0; $i < sizeof($mastervehicle); $i++) {
			$jsonautocheck = json_decode($mastervehicle[$i]['vehicle_autocheck']);
			if (isset($jsonautocheck->auto_status)) {
				// code...
			$auto_status   = $jsonautocheck->auto_status;

			if ($privilegecode == 5 || $privilegecode == 6) {
				if ($mastervehicle[$i]['vehicle_company'] == $user_company) {
					if ($jsonautocheck->auto_last_engine == "ON") {
						$statusvehicle['engine_on'] += 1;
					}else {
						$statusvehicle['engine_off'] += 1;
					}
				}
			}else {
				if ($jsonautocheck->auto_last_engine == "ON") {
					$statusvehicle['engine_on'] += 1;
				}else {
					$statusvehicle['engine_off'] += 1;
				}
			}

				if ($auto_status != "M") {
					array_push($datafix, array(
						"vehicle_id"             => $mastervehicle[$i]['vehicle_id'],
						"vehicle_user_id"        => $mastervehicle[$i]['vehicle_user_id'],
						"vehicle_company"        => $mastervehicle[$i]['vehicle_company'],
						"vehicle_device"         => $mastervehicle[$i]['vehicle_device'],
						"vehicle_no"             => $mastervehicle[$i]['vehicle_no'],
						"vehicle_name"           => $mastervehicle[$i]['vehicle_name'],
						"vehicle_active_date2"   => $mastervehicle[$i]['vehicle_active_date2'],
						"auto_last_lat"          => substr($jsonautocheck->auto_last_lat, 0, 10),
						"auto_last_long"         => substr($jsonautocheck->auto_last_long, 0, 10),
					));
				}
			}
		}

		$company                  = $this->dashboardmodel->getcompany_byowner($privilegecode);
			if ($company) {

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
			}else {
				$this->params['company']   = 0;
				$this->params['companyid'] = 0;
				$this->params['vehicle']   = 0;
			}

		// echo "<pre>";
		// var_dump($company);die();
		// echo "<pre>";


		$this->params['url_code_view']  = "1";
		$this->params['code_view_menu'] = "monitor";
		$this->params['maps_code']      = "morehundred";

		$this->params['engine_on']      = $statusvehicle['engine_on'];
		$this->params['engine_off']     = $statusvehicle['engine_off'];


		$rstatus                        = $this->dashboardmodel->gettotalstatus($this->sess->user_id);

		$datastatus                     = explode("|", $rstatus);
		$this->params['total_online']   = $datastatus[0]+$datastatus[1]; //p + K
		$this->params['total_vehicle']  = $datastatus[3];
		$this->params['total_offline']  = $datastatus[2];

		$this->params['vehicledata']  = $datafix;
		$this->params['vehicletotal'] = sizeof($mastervehicle);
		$this->params['poolmaster']   = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");
		$getvehicle_byowner           = $this->dashboardmodel->getvehicle_byownerforheatmap();
		// echo "<pre>";
		// var_dump($getvehicle_byowner);die();
		// echo "<pre>";
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
		$this->params['mapsetting']     = $this->m_poipoolmaster->getmapsetting();
		$this->params['poolmaster']     = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");

		// echo "<pre>";
		// var_dump($this->params['mapsetting']);die();
		// echo "<pre>";

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

			if ($privilegecode == 1) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/singleurl/v_home_heatmap', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
			}elseif ($privilegecode == 2) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/singleurl/v_home_heatmap', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
			}elseif ($privilegecode == 3) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/singleurl/v_home_heatmap', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
			}elseif ($privilegecode == 4) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/singleurl/v_home_heatmap', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
			}elseif ($privilegecode == 5) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/singleurl/v_home_heatmap', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
			}elseif ($privilegecode == 6) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/singleurl/v_home_heatmap', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
			}else {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/singleurl/v_home_heatmap', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
			}
	}

	function singleoutofhauling(){
		ini_set('max_execution_time', '300');
		set_time_limit(300);
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$user_id       = $this->sess->user_id;
		$user_parent   = $this->sess->user_parent;
		$privilegecode = $this->sess->user_id_role;
		$user_company  = $this->sess->user_company;

		if($privilegecode == 0){
			$user_id_fix = $user_id;
		}elseif ($privilegecode == 1) {
			$user_id_fix = $user_parent;
		}elseif ($privilegecode == 2) {
			$user_id_fix = $user_parent;
		}elseif ($privilegecode == 3) {
			$user_id_fix = $user_parent;
		}elseif ($privilegecode == 4) {
			$user_id_fix = $user_parent;
		}elseif ($privilegecode == 5) {
			$user_id_fix = $user_id;
		}elseif ($privilegecode == 6) {
			$user_id_fix = $user_id;
		}else{
			$user_id_fix = $user_id;
		}

		$companyid                       = $this->sess->user_company;
		$user_dblive                     = $this->sess->user_dblive;
		$mastervehicle                   = $this->m_poipoolmaster->getmastervehicleforheatmap();

		$datafix                         = array();
		$deviceidygtidakada              = array();
		$statusvehicle['engine_on']  = 0;
		$statusvehicle['engine_off'] = 0;

		for ($i=0; $i < sizeof($mastervehicle); $i++) {
			$jsonautocheck = json_decode($mastervehicle[$i]['vehicle_autocheck']);
			if (isset($jsonautocheck->auto_status)) {
				// code...
			$auto_status   = $jsonautocheck->auto_status;

			if ($privilegecode == 5 || $privilegecode == 6) {
				if ($mastervehicle[$i]['vehicle_company'] == $user_company) {
					if ($jsonautocheck->auto_last_engine == "ON") {
						$statusvehicle['engine_on'] += 1;
					}else {
						$statusvehicle['engine_off'] += 1;
					}
				}
			}else {
				if ($jsonautocheck->auto_last_engine == "ON") {
					$statusvehicle['engine_on'] += 1;
				}else {
					$statusvehicle['engine_off'] += 1;
				}
			}

				if ($auto_status != "M") {
					array_push($datafix, array(
						"vehicle_id"             => $mastervehicle[$i]['vehicle_id'],
						"vehicle_user_id"        => $mastervehicle[$i]['vehicle_user_id'],
						"vehicle_company"        => $mastervehicle[$i]['vehicle_company'],
						"vehicle_device"         => $mastervehicle[$i]['vehicle_device'],
						"vehicle_no"             => $mastervehicle[$i]['vehicle_no'],
						"vehicle_name"           => $mastervehicle[$i]['vehicle_name'],
						"vehicle_active_date2"   => $mastervehicle[$i]['vehicle_active_date2'],
						"auto_last_lat"          => substr($jsonautocheck->auto_last_lat, 0, 10),
						"auto_last_long"         => substr($jsonautocheck->auto_last_long, 0, 10),
					));
				}
			}
		}

		$company                  = $this->dashboardmodel->getcompany_byowner($privilegecode);
			if ($company) {

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
			}else {
				$this->params['company']   = 0;
				$this->params['companyid'] = 0;
				$this->params['vehicle']   = 0;
			}

		// echo "<pre>";
		// var_dump($company);die();
		// echo "<pre>";


		$this->params['url_code_view']  = "1";
		$this->params['code_view_menu'] = "monitor";
		$this->params['maps_code']      = "morehundred";

		$this->params['engine_on']      = $statusvehicle['engine_on'];
		$this->params['engine_off']     = $statusvehicle['engine_off'];


		$rstatus                        = $this->dashboardmodel->gettotalstatus($this->sess->user_id);

		$datastatus                     = explode("|", $rstatus);
		$this->params['total_online']   = $datastatus[0]+$datastatus[1]; //p + K
		$this->params['total_vehicle']  = $datastatus[3];
		$this->params['total_offline']  = $datastatus[2];

		$this->params['vehicledata']  = $datafix;
		$this->params['vehicletotal'] = sizeof($mastervehicle);
		$this->params['poolmaster']   = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");
		$getvehicle_byowner           = $this->dashboardmodel->getvehicle_byownerforheatmap();
		// echo "<pre>";
		// var_dump($getvehicle_byowner);die();
		// echo "<pre>";
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
		$this->params['mapsetting']     = $this->m_poipoolmaster->getmapsetting();
		$this->params['poolmaster']     = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");

		// echo "<pre>";
		// var_dump($this->params['mapsetting']);die();
		// echo "<pre>";

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

			if ($privilegecode == 1) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/singleurl/v_home_outofhauling', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
			}elseif ($privilegecode == 2) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/singleurl/v_home_outofhauling', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
			}elseif ($privilegecode == 3) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/singleurl/v_home_outofhauling', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
			}elseif ($privilegecode == 4) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/singleurl/v_home_outofhauling', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
			}elseif ($privilegecode == 5) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/singleurl/v_home_outofhauling', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
			}elseif ($privilegecode == 6) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/singleurl/v_home_outofhauling', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
			}else {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/singleurl/v_home_outofhauling', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
			}
	}

	function singlemapsstandard(){
		ini_set('max_execution_time', '300');
		set_time_limit(300);
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$user_id       = $this->sess->user_id;
		$user_parent   = $this->sess->user_parent;
		$privilegecode = $this->sess->user_id_role;
		$user_company  = $this->sess->user_company;

		if($privilegecode == 0){
			$user_id_fix = $user_id;
		}elseif ($privilegecode == 1) {
			$user_id_fix = $user_parent;
		}elseif ($privilegecode == 2) {
			$user_id_fix = $user_parent;
		}elseif ($privilegecode == 3) {
			$user_id_fix = $user_parent;
		}elseif ($privilegecode == 4) {
			$user_id_fix = $user_parent;
		}elseif ($privilegecode == 5) {
			$user_id_fix = $user_id;
		}elseif ($privilegecode == 6) {
			$user_id_fix = $user_id;
		}else{
			$user_id_fix = $user_id;
		}

		$companyid                       = $this->sess->user_company;
		$user_dblive                     = $this->sess->user_dblive;
		$mastervehicle                   = $this->m_poipoolmaster->getmastervehicleforheatmap();

		$datafix                         = array();
		$deviceidygtidakada              = array();
		$statusvehicle['engine_on']  = 0;
		$statusvehicle['engine_off'] = 0;

		for ($i=0; $i < sizeof($mastervehicle); $i++) {
			$jsonautocheck = json_decode($mastervehicle[$i]['vehicle_autocheck']);
			if (isset($jsonautocheck->auto_status)) {
				// code...
			$auto_status   = $jsonautocheck->auto_status;

			if ($privilegecode == 5 || $privilegecode == 6) {
				if ($mastervehicle[$i]['vehicle_company'] == $user_company) {
					if ($jsonautocheck->auto_last_engine == "ON") {
						$statusvehicle['engine_on'] += 1;
					}else {
						$statusvehicle['engine_off'] += 1;
					}
				}
			}else {
				if ($jsonautocheck->auto_last_engine == "ON") {
					$statusvehicle['engine_on'] += 1;
				}else {
					$statusvehicle['engine_off'] += 1;
				}
			}

				if ($auto_status != "M") {
					array_push($datafix, array(
						"vehicle_id"             => $mastervehicle[$i]['vehicle_id'],
						"vehicle_user_id"        => $mastervehicle[$i]['vehicle_user_id'],
						"vehicle_company"        => $mastervehicle[$i]['vehicle_company'],
						"vehicle_device"         => $mastervehicle[$i]['vehicle_device'],
						"vehicle_no"             => $mastervehicle[$i]['vehicle_no'],
						"vehicle_name"           => $mastervehicle[$i]['vehicle_name'],
						"vehicle_active_date2"   => $mastervehicle[$i]['vehicle_active_date2'],
						"auto_last_lat"          => substr($jsonautocheck->auto_last_lat, 0, 10),
						"auto_last_long"         => substr($jsonautocheck->auto_last_long, 0, 10),
					));
				}
			}
		}

		$company                  = $this->dashboardmodel->getcompany_byowner($privilegecode);
			if ($company) {

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
			}else {
				$this->params['company']   = 0;
				$this->params['companyid'] = 0;
				$this->params['vehicle']   = 0;
			}

		// echo "<pre>";
		// var_dump($company);die();
		// echo "<pre>";


		$this->params['url_code_view']  = "1";
		$this->params['code_view_menu'] = "monitor";
		$this->params['maps_code']      = "morehundred";

		$this->params['engine_on']      = $statusvehicle['engine_on'];
		$this->params['engine_off']     = $statusvehicle['engine_off'];


		$rstatus                        = $this->dashboardmodel->gettotalstatus($this->sess->user_id);

		$datastatus                     = explode("|", $rstatus);
		$this->params['total_online']   = $datastatus[0]+$datastatus[1]; //p + K
		$this->params['total_vehicle']  = $datastatus[3];
		$this->params['total_offline']  = $datastatus[2];

		$this->params['vehicledata']  = $datafix;
		$this->params['vehicletotal'] = sizeof($mastervehicle);
		$this->params['poolmaster']   = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");
		$getvehicle_byowner           = $this->dashboardmodel->getvehicle_byownerforheatmap();
		// echo "<pre>";
		// var_dump($getvehicle_byowner);die();
		// echo "<pre>";
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
		$this->params['mapsetting']     = $this->m_poipoolmaster->getmapsetting();
		$this->params['poolmaster']     = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");

		// echo "<pre>";
		// var_dump($this->params['mapsetting']);die();
		// echo "<pre>";

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

			if ($privilegecode == 1) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/singleurl/v_home_mapsstandard', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
			}elseif ($privilegecode == 2) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/singleurl/v_home_mapsstandard', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
			}elseif ($privilegecode == 3) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/singleurl/v_home_mapsstandard', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
			}elseif ($privilegecode == 4) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/singleurl/v_home_mapsstandard', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
			}elseif ($privilegecode == 5) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/singleurl/v_home_mapsstandard', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
			}elseif ($privilegecode == 6) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/singleurl/v_home_mapsstandard', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
			}else {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/singleurl/v_home_mapsstandard', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
			}
	}
//FUNCTION KHUSUS SINGLE URL END

// ADMIN ONLY
// UNTUK TESTING
// function heatmapoprekantestingotomasimapsoption(){
	function devmonitoringnew(){
		ini_set('max_execution_time', '300');
		set_time_limit(300);
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$user_id       = $this->sess->user_id;
		$user_parent   = $this->sess->user_parent;
		$privilegecode = $this->sess->user_id_role;
		$user_company  = $this->sess->user_company;

		if($privilegecode == 0){
			$user_id_fix = $user_id;
		}elseif ($privilegecode == 1) {
			$user_id_fix = $user_parent;
		}elseif ($privilegecode == 2) {
			$user_id_fix = $user_parent;
		}elseif ($privilegecode == 3) {
			$user_id_fix = $user_parent;
		}elseif ($privilegecode == 4) {
			$user_id_fix = $user_parent;
		}elseif ($privilegecode == 5) {
			$user_id_fix = $user_id;
		}elseif ($privilegecode == 6) {
			$user_id_fix = $user_id;
		}else{
			$user_id_fix = $user_id;
		}

		$companyid                     = $this->sess->user_company;
		$user_dblive                   = $this->sess->user_dblive;
		$mastervehicle                 = $this->m_poipoolmaster->mastervehicleadminonly();

		// echo "<pre>";
		// var_dump($mastervehicle);die();
		// echo "<pre>";

		$datafix                       = array();
		$deviceidygtidakada            = array();
		$statusvehicle['engine_on']    = 0;
		$statusvehicle['engine_off']   = 0;
		$status_lain_P								 = array();
		$status_lain_K								 = array();
		$status_lain_M								 = array();
		$statusvehicle['status_lain2'] = 0;


		for ($i=0; $i < sizeof($mastervehicle); $i++) {
			$jsonautocheck = json_decode($mastervehicle[$i]['vehicle_autocheck']);
			if (isset($jsonautocheck->auto_status)) {
				$auto_status   = $jsonautocheck->auto_status;

				if ($privilegecode == 5 || $privilegecode == 6) {
					if ($mastervehicle[$i]['vehicle_company'] == $user_company) {
						if ($jsonautocheck->auto_last_engine == "ON") {
							$statusvehicle['engine_on'] += 1;
						}else {
							$statusvehicle['engine_off'] += 1;
						}
					}
				}else {
					if ($jsonautocheck->auto_last_engine == "ON") {
						$statusvehicle['engine_on'] += 1;
					}else {
						$statusvehicle['engine_off'] += 1;
					}
				}

				// echo "<pre>";
				// var_dump($status_lain1);die();
				// echo "<pre>";

				if ($auto_status != "M") {
					array_push($status_lain_P, array(
						"auto_status" => $auto_status
					));
				}

				if ($auto_status == "K") {
					array_push($status_lain_K, array(
						"auto_status" => $auto_status
					));
				}

				if ($auto_status == "M") {
					array_push($status_lain_M, array(
						"auto_status" => $auto_status
					));
				}

					if ($auto_status != "M") {
						array_push($datafix, array(
							"vehicle_id"             => $mastervehicle[$i]['vehicle_id'],
							"vehicle_user_id"        => $mastervehicle[$i]['vehicle_user_id'],
							"vehicle_company"        => $mastervehicle[$i]['vehicle_company'],
							"vehicle_device"         => $mastervehicle[$i]['vehicle_device'],
							"vehicle_no"             => $mastervehicle[$i]['vehicle_no'],
							"vehicle_name"           => $mastervehicle[$i]['vehicle_name'],
							"vehicle_active_date2"   => $mastervehicle[$i]['vehicle_active_date2'],
							"auto_last_lat"          => substr($jsonautocheck->auto_last_lat, 0, 10),
							"auto_last_long"         => substr($jsonautocheck->auto_last_long, 0, 10),
						));
					}
			}

		}

		$count_P = sizeof($status_lain_P);
		$count_K = sizeof($status_lain_K);
		$count_M = sizeof($status_lain_M);

		// echo "<pre>";
		// var_dump($count_P.'-'.$count_K.'-'.$count_M);die();
		// echo "<pre>";

		$company                  = $this->dashboardmodel->getcompany_byowner($privilegecode);
			if ($company) {

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
			}else {
				$this->params['company']   = 0;
				$this->params['companyid'] = 0;
				$this->params['vehicle']   = 0;
			}

		// echo "<pre>";
		// var_dump($company);die();
		// echo "<pre>";


		$this->params['url_code_view']  = "1";
		$this->params['code_view_menu'] = "monitoradminonly";
		$this->params['maps_code']      = "morehundred";

		$this->params['engine_on']      = $statusvehicle['engine_on'];
		$this->params['engine_off']     = $statusvehicle['engine_off'];


		$rstatus                        = $this->dashboardmodel->gettotalstatus($this->sess->user_id);

		$datastatus                     = explode("|", $rstatus);
		$this->params['total_online']   = $datastatus[0]+$datastatus[1]; //p + K
		$this->params['total_vehicle']  = $datastatus[3];
		$this->params['total_offline']  = $datastatus[2];

		$this->params['vehicledata']  = $datafix;
		$this->params['vehicles']  = $mastervehicle;
		$this->params['vehicletotal'] = sizeof($mastervehicle);
		$this->params['poolmaster']   = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");
		$getvehicle_byowner           = $this->dashboardmodel->getvehicle_byownerforheatmap();
		// echo "<pre>";
		// var_dump($getvehicle_byowner);die();
		// echo "<pre>";
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
		$this->params['mapsetting']     = $this->m_poipoolmaster->getmapsetting();
		$this->params['poolmaster']     = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");

		// echo "<pre>";
		// var_dump($this->params['mapsetting']);die();
		// echo "<pre>";

	$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
	$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/trackers/maps_view_heatmapoprekan2', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		}elseif ($privilegecode == 2) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/trackers/maps_view_heatmapoprekan2', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
		}elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/trackers/maps_view_heatmapoprekan2', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		}elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/trackers/maps_view_heatmapoprekan2', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		}elseif ($privilegecode == 5) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/trackers/maps_view_heatmapoprekan2', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
		}elseif ($privilegecode == 6) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/trackers/maps_view_heatmapoprekan2', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
		}else {
			// $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			// $this->params["content"]        = $this->load->view('newdashboard/trackers/maps_view_heatmapoprekan2', $this->params, true);
			// $this->load->view("newdashboard/partial/template_dashboard_new", $this->params);

			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_plan', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/trackers/maps_view_heatmapoprekan2', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
}

function km_quickcount_newadminonly(){
	$companyid 				 = $_POST['companyid'];
	$masterdatavehicle = $this->m_poipoolmaster->getmastervehiclebycontractoradminonly($companyid);
	$dataKmMuatanFix   = array();
	$dataKmKosonganFix = array();

	// echo "<pre>";
	// var_dump($masterdatavehicle);die();
	// echo "<pre>";

	$dataJumlahInKmKosongan_2['gb0_port_bib_kosongan_1']    = 0;
	$dataJumlahInKmKosongan_2['gb1_port_bib_kosongan_2']    = 0;
	$dataJumlahInKmKosongan_2['gb2_port_bir_kosongan_1']    = 0;
	$dataJumlahInKmKosongan_2['gb3_port_bir_kosongan_2']    = 0;
	$dataJumlahInKmKosongan_2['gb4_simpang_bayah_kosongan'] = 0;

	$dataJumlahInKmMuatan_2['gb5_port_bib_antrian']         = 0;
	$dataJumlahInKmMuatan_2['gb6_port_bir_antrian_wb']      = 0;

	$dataJumlahInKmMuatan['KM_0']  = 0;
	$dataJumlahInKmMuatan['KM_1']  = 0;
	$dataJumlahInKmMuatan['KM_2']  = 0;
	$dataJumlahInKmMuatan['KM_3']  = 0;
	$dataJumlahInKmMuatan['KM_4']  = 0;
	$dataJumlahInKmMuatan['KM_5']  = 0;
	$dataJumlahInKmMuatan['KM_6']  = 0;
	$dataJumlahInKmMuatan['KM_7']  = 0;
	$dataJumlahInKmMuatan['KM_8']  = 0;
	$dataJumlahInKmMuatan['KM_9']  = 0;
	$dataJumlahInKmMuatan['KM_10'] = 0;
	$dataJumlahInKmMuatan['KM_11'] = 0;
	$dataJumlahInKmMuatan['KM_12'] = 0;
	$dataJumlahInKmMuatan['KM_13'] = 0;
	$dataJumlahInKmMuatan['KM_14'] = 0;
	$dataJumlahInKmMuatan['KM_15'] = 0;
	$dataJumlahInKmMuatan['KM_16'] = 0;
	$dataJumlahInKmMuatan['KM_17'] = 0;
	$dataJumlahInKmMuatan['KM_18'] = 0;
	$dataJumlahInKmMuatan['KM_19'] = 0;
	$dataJumlahInKmMuatan['KM_20'] = 0;
	$dataJumlahInKmMuatan['KM_21'] = 0;
	$dataJumlahInKmMuatan['KM_22'] = 0;
	$dataJumlahInKmMuatan['KM_23'] = 0;
	$dataJumlahInKmMuatan['KM_24'] = 0;
	$dataJumlahInKmMuatan['KM_25'] = 0;
	$dataJumlahInKmMuatan['KM_26'] = 0;
	$dataJumlahInKmMuatan['KM_27'] = 0;
	$dataJumlahInKmMuatan['KM_28'] = 0;
	$dataJumlahInKmMuatan['KM_29'] = 0;
	$dataJumlahInKmMuatan['KM_30'] = 0;

	$dataJumlahInKmKosongan['KM_0']  = 0;
	$dataJumlahInKmKosongan['KM_1']  = 0;
	$dataJumlahInKmKosongan['KM_2']  = 0;
	$dataJumlahInKmKosongan['KM_3']  = 0;
	$dataJumlahInKmKosongan['KM_4']  = 0;
	$dataJumlahInKmKosongan['KM_5']  = 0;
	$dataJumlahInKmKosongan['KM_6']  = 0;
	$dataJumlahInKmKosongan['KM_7']  = 0;
	$dataJumlahInKmKosongan['KM_8']  = 0;
	$dataJumlahInKmKosongan['KM_9']  = 0;
	$dataJumlahInKmKosongan['KM_10'] = 0;
	$dataJumlahInKmKosongan['KM_11'] = 0;
	$dataJumlahInKmKosongan['KM_12'] = 0;
	$dataJumlahInKmKosongan['KM_13'] = 0;
	$dataJumlahInKmKosongan['KM_14'] = 0;
	$dataJumlahInKmKosongan['KM_15'] = 0;
	$dataJumlahInKmKosongan['KM_16'] = 0;
	$dataJumlahInKmKosongan['KM_17'] = 0;
	$dataJumlahInKmKosongan['KM_18'] = 0;
	$dataJumlahInKmKosongan['KM_19'] = 0;
	$dataJumlahInKmKosongan['KM_20'] = 0;
	$dataJumlahInKmKosongan['KM_21'] = 0;
	$dataJumlahInKmKosongan['KM_22'] = 0;
	$dataJumlahInKmKosongan['KM_23'] = 0;
	$dataJumlahInKmKosongan['KM_24'] = 0;
	$dataJumlahInKmKosongan['KM_25'] = 0;
	$dataJumlahInKmKosongan['KM_26'] = 0;
	$dataJumlahInKmKosongan['KM_27'] = 0;
	$dataJumlahInKmKosongan['KM_28'] = 0;
	$dataJumlahInKmKosongan['KM_29'] = 0;
	$dataJumlahInKmKosongan['KM_30'] = 0;

	$lasttimecheck = date("d-m-Y H:i:s", strtotime("+1 hour"));

	for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
		$autocheck          = json_decode($masterdatavehicle[$i]['vehicle_autocheck']);
		$auto_last_position = explode(",", $autocheck->auto_last_position);
		$jalur_name         = $autocheck->auto_last_road;
		$datalastposition   = $auto_last_position[0];
		$auto_status 				= $autocheck->auto_status;

		// echo "<pre>";
		// var_dump($auto_status);die();
		// echo "<pre>";

			if ($auto_status != "M") {
				if ($jalur_name == "kosongan") {
					if ($datalastposition == "Port BIB - Kosongan 1") {
						$dataJumlahInKmKosongan_2['gb0_port_bib_kosongan_1'] += 1;
					}elseif ($datalastposition == "Port BIB - Kosongan 2") {
						$dataJumlahInKmKosongan_2['gb1_port_bib_kosongan_2'] += 1;
					}elseif ($datalastposition == "Port BIR - Kosongan 1") {
						$dataJumlahInKmKosongan_2['gb2_port_bir_kosongan_1'] += 1;
					}elseif ($datalastposition == "Port BIR - Kosongan 2") {
						$dataJumlahInKmKosongan_2['gb3_port_bir_kosongan_2'] += 1;
					}elseif ($datalastposition == "Simpang Bayah - Kosongan") {
						$dataJumlahInKmKosongan_2['gb4_simpang_bayah_kosongan'] += 1;
					}elseif ($datalastposition == "KM 1" || $datalastposition == "KM 1.5" || $datalastposition == "KM 0.5") {
						$dataJumlahInKmKosongan['KM_2'] += 1;
					}elseif ($datalastposition == "KM 2" || $datalastposition == "KM 2.5") {
						$dataJumlahInKmKosongan['KM_3'] += 1;
					}elseif ($datalastposition == "KM 3" || $datalastposition == "KM 3.5") {
						$dataJumlahInKmKosongan['KM_4'] += 1;
					}elseif ($datalastposition == "KM 4" || $datalastposition == "KM 4.5") {
						$dataJumlahInKmKosongan['KM_5'] += 1;
					}elseif ($datalastposition == "KM 5" || $datalastposition == "KM 5.5") {
						$dataJumlahInKmKosongan['KM_6'] += 1;
					}elseif ($datalastposition == "KM 6" || $datalastposition == "KM 6.5") {
						$dataJumlahInKmKosongan['KM_7'] += 1;
					}elseif ($datalastposition == "KM 7" || $datalastposition == "KM 7.5") {
						$dataJumlahInKmKosongan['KM_8'] += 1;
					}elseif ($datalastposition == "KM 8" || $datalastposition == "KM 8.5") {
						$dataJumlahInKmKosongan['KM_9'] += 1;
					}elseif ($datalastposition == "KM 9" || $datalastposition == "KM 9.5") {
						$dataJumlahInKmKosongan['KM_10'] += 1;
					}elseif ($datalastposition == "KM 10" || $datalastposition == "KM 10.5") {
						$dataJumlahInKmKosongan['KM_11'] += 1;
					}elseif ($datalastposition == "KM 11" || $datalastposition == "KM 11.5") {
						$dataJumlahInKmKosongan['KM_12'] += 1;
					}elseif ($datalastposition == "KM 12" || $datalastposition == "KM 12.5") {
						$dataJumlahInKmKosongan['KM_13'] += 1;
					}elseif ($datalastposition == "KM 13" || $datalastposition == "KM 13.5") {
						$dataJumlahInKmKosongan['KM_14'] += 1;
					}elseif ($datalastposition == "KM 14" || $datalastposition == "KM 14.5") {
						$dataJumlahInKmKosongan['KM_15'] += 1;
					}elseif ($datalastposition == "KM 15" || $datalastposition == "KM 15.5") {
						$dataJumlahInKmKosongan['KM_16'] += 1;
					}elseif ($datalastposition == "KM 16" || $datalastposition == "KM 16.5") {
						$dataJumlahInKmKosongan['KM_17'] += 1;
					}elseif ($datalastposition == "KM 17" || $datalastposition == "KM 17.5") {
						$dataJumlahInKmKosongan['KM_18'] += 1;
					}elseif ($datalastposition == "KM 18" || $datalastposition == "KM 18.5") {
						$dataJumlahInKmKosongan['KM_19'] += 1;
					}elseif ($datalastposition == "KM 19" || $datalastposition == "KM 19.5") {
						$dataJumlahInKmKosongan['KM_20'] += 1;
					}elseif ($datalastposition == "KM 20" || $datalastposition == "KM 20.5") {
						$dataJumlahInKmKosongan['KM_21'] += 1;
					}elseif ($datalastposition == "KM 21" || $datalastposition == "KM 21.5") {
						$dataJumlahInKmKosongan['KM_22'] += 1;
					}elseif ($datalastposition == "KM 22" || $datalastposition == "KM 22.5") {
						$dataJumlahInKmKosongan['KM_23'] += 1;
					}elseif ($datalastposition == "KM 23" || $datalastposition == "KM 23.5") {
						$dataJumlahInKmKosongan['KM_24'] += 1;
					}elseif ($datalastposition == "KM 24" || $datalastposition == "KM 24.5") {
						$dataJumlahInKmKosongan['KM_25'] += 1;
					}elseif ($datalastposition == "KM 25" || $datalastposition == "KM 25.5") {
						$dataJumlahInKmKosongan['KM_26'] += 1;
					}elseif ($datalastposition == "KM 26" || $datalastposition == "KM 26.5") {
						$dataJumlahInKmKosongan['KM_27'] += 1;
					}elseif ($datalastposition == "KM 27" || $datalastposition == "KM 27.5") {
						$dataJumlahInKmKosongan['KM_28'] += 1;
					}elseif ($datalastposition == "KM 28" || $datalastposition == "KM 28.5") {
						$dataJumlahInKmKosongan['KM_29'] += 1;
					}elseif ($datalastposition == "KM 29" || $datalastposition == "KM 29.5") {
						$dataJumlahInKmKosongan['KM_30'] += 1;
					}
				}else {
					if ($datalastposition == "Port BIB - Antrian") {
						$dataJumlahInKmMuatan_2['gb5_port_bib_antrian'] += 1;
					}elseif ($datalastposition == "Port BIR - Antrian WB") {
						$dataJumlahInKmMuatan_2['gb6_port_bir_antrian_wb'] += 1;
					}elseif ($datalastposition == "KM 1" || $datalastposition == "KM 1.5" || $datalastposition == "KM 0.5") {
						$dataJumlahInKmMuatan['KM_2'] += 1;
					}elseif ($datalastposition == "KM 2" || $datalastposition == "KM 2.5") {
						$dataJumlahInKmMuatan['KM_3'] += 1;
					}elseif ($datalastposition == "KM 3" || $datalastposition == "KM 3.5") {
						$dataJumlahInKmMuatan['KM_4'] += 1;
					}elseif ($datalastposition == "KM 4" || $datalastposition == "KM 4.5") {
						$dataJumlahInKmMuatan['KM_5'] += 1;
					}elseif ($datalastposition == "KM 5" || $datalastposition == "KM 5.5") {
						$dataJumlahInKmMuatan['KM_6'] += 1;
					}elseif ($datalastposition == "KM 6" || $datalastposition == "KM 6.5") {
						$dataJumlahInKmMuatan['KM_7'] += 1;
					}elseif ($datalastposition == "KM 7" || $datalastposition == "KM 7.5") {
						$dataJumlahInKmMuatan['KM_8'] += 1;
					}elseif ($datalastposition == "KM 8" || $datalastposition == "KM 8.5") {
						$dataJumlahInKmMuatan['KM_9'] += 1;
					}elseif ($datalastposition == "KM 9" || $datalastposition == "KM 9.5") {
						$dataJumlahInKmMuatan['KM_10'] += 1;
					}elseif ($datalastposition == "KM 10" || $datalastposition == "KM 10.5") {
						$dataJumlahInKmMuatan['KM_11'] += 1;
					}elseif ($datalastposition == "KM 11" || $datalastposition == "KM 11.5") {
						$dataJumlahInKmMuatan['KM_12'] += 1;
					}elseif ($datalastposition == "KM 12" || $datalastposition == "KM 12.5") {
						$dataJumlahInKmMuatan['KM_13'] += 1;
					}elseif ($datalastposition == "KM 13" || $datalastposition == "KM 13.5") {
						$dataJumlahInKmMuatan['KM_14'] += 1;
					}elseif ($datalastposition == "KM 14" || $datalastposition == "KM 14.5") {
						$dataJumlahInKmMuatan['KM_15'] += 1;
					}elseif ($datalastposition == "KM 15" || $datalastposition == "KM 15.5") {
						$dataJumlahInKmMuatan['KM_16'] += 1;
					}elseif ($datalastposition == "KM 16" || $datalastposition == "KM 16.5") {
						$dataJumlahInKmMuatan['KM_17'] += 1;
					}elseif ($datalastposition == "KM 17" || $datalastposition == "KM 17.5") {
						$dataJumlahInKmMuatan['KM_18'] += 1;
					}elseif ($datalastposition == "KM 18" || $datalastposition == "KM 18.5") {
						$dataJumlahInKmMuatan['KM_19'] += 1;
					}elseif ($datalastposition == "KM 19" || $datalastposition == "KM 19.5") {
						$dataJumlahInKmMuatan['KM_20'] += 1;
					}elseif ($datalastposition == "KM 20" || $datalastposition == "KM 20.5") {
						$dataJumlahInKmMuatan['KM_21'] += 1;
					}elseif ($datalastposition == "KM 21" || $datalastposition == "KM 21.5") {
						$dataJumlahInKmMuatan['KM_22'] += 1;
					}elseif ($datalastposition == "KM 22" || $datalastposition == "KM 22.5") {
						$dataJumlahInKmMuatan['KM_23'] += 1;
					}elseif ($datalastposition == "KM 23" || $datalastposition == "KM 23.5") {
						$dataJumlahInKmMuatan['KM_24'] += 1;
					}elseif ($datalastposition == "KM 24" || $datalastposition == "KM 24.5") {
						$dataJumlahInKmMuatan['KM_25'] += 1;
					}elseif ($datalastposition == "KM 25" || $datalastposition == "KM 25.5") {
						$dataJumlahInKmMuatan['KM_26'] += 1;
					}elseif ($datalastposition == "KM 26" || $datalastposition == "KM 26.5") {
						$dataJumlahInKmMuatan['KM_27'] += 1;
					}elseif ($datalastposition == "KM 27" || $datalastposition == "KM 27.5") {
						$dataJumlahInKmMuatan['KM_28'] += 1;
					}elseif ($datalastposition == "KM 28" || $datalastposition == "KM 28.5") {
						$dataJumlahInKmMuatan['KM_29'] += 1;
					}elseif ($datalastposition == "KM 29" || $datalastposition == "KM 29.5") {
						$dataJumlahInKmMuatan['KM_30'] += 1;
					}
				}
			}
	}

	// LIMIT SETTING PER KM
	$arraynotin              = array("Port BIR - Kosongan 2", "Port BIB - Kosongan 2", "Simpang Bayah - Kosongan", "Port BIR - Antrian WB", "Port BIB - Kosongan 1",
																		"Port BIB - Antrian", "Port BIR - Kosongan 1", "KM 1");

	$arraynotinAllKMKosongan = array("Port BIR - Kosongan 2", "Port BIB - Kosongan 2", "Simpang Bayah - Kosongan", "Port BIR - Antrian WB", "Port BIB - Kosongan 1",
																		"Port BIB - Antrian", "Port BIR - Kosongan 1");
  $arrayinKM1Muatan	 			 = array("KM 1");

	$getdataFromStreet = $this->m_poipoolmaster->getstreet_now(1);
	$mapSettingType    = $this->m_poipoolmaster->getMapSettingByType(1);

	$postfix_middle_limit_allkmmuatan   = "_middle_limit_allkmmuatan";
	$postfix_top_limit_allkmmuatan      = "_top_limit_allkmmuatan";
	$postfix_middle_limit_allkmkosongan = "_middle_limit_allkmkosongan";
	$postfix_top_limit_allkmkosongan    = "_top_limit_allkmkosongan";
	$postfix_bottom_limit_km1muatan     = "_bottom_limit_km1muatan";
	$postfix_middle_limit_km1muatan     = "_middle_limit_km1muatan";
	$postfix_top_limit_km1muatan        = "_top_limit_km1muatan";

	// LIMIT ONLY KM 1
	if (isset($getdataFromStreet)) {
		$datafixlimitkm1muatan = array();
		for ($i=0; $i < sizeof($getdataFromStreet); $i++) {
			$streetremovecoma         = explode(",", $getdataFromStreet[$i]['street_name']);
				if (in_array($streetremovecoma[0], $arrayinKM1Muatan)) {
					$streetfix                = str_replace(" ", "_", $streetremovecoma[0]);
					$bottomlimitname          = $streetfix.$postfix_bottom_limit_km1muatan;
					$middlelimitname          = $streetfix.$postfix_middle_limit_km1muatan;
					$toplimitname             = $streetfix.$postfix_top_limit_km1muatan;
					$getMapSettingByLimitName = $this->m_poipoolmaster->getThisMapSettingByLimitName_mapsetting_onlykm1($bottomlimitname, $middlelimitname, $toplimitname);

					// echo "<pre>";
					// var_dump($getMapSettingByLimitName);die();
					// echo "<pre>";

					if (sizeof($getMapSettingByLimitName) > 1) {
							array_push($datafixlimitkm1muatan, array(
								"street_id"               => $getdataFromStreet[$i]['street_id'],
								"street_name"             => $getdataFromStreet[$i]['street_name'],
								"mapsetting_type"         => 1,
								"mapsetting_name_alias"   => $streetremovecoma[0],
								"mapsetting_name"         => $streetfix,
								"mapsetting_bottom_limit" => $getMapSettingByLimitName[0]['mapsetting_limit_value'],
								"mapsetting_middle_limit" => $getMapSettingByLimitName[1]['mapsetting_limit_value'],
								"mapsetting_top_limit"    => $getMapSettingByLimitName[2]['mapsetting_limit_value']
							));
					}else {
						array_push($datafixlimitkm1muatan, array(
							"street_id"               => $getdataFromStreet[$i]['street_id'],
							"street_name"             => $getdataFromStreet[$i]['street_name'],
							"mapsetting_type"         => 1,
							"mapsetting_name_alias"   => $streetremovecoma[0],
							"mapsetting_name"         => $streetfix,
							"mapsetting_bottom_limit" => 0,
							"mapsetting_middle_limit" => 0,
							"mapsetting_top_limit"    => 0
						));
					}
				}
		}
	}

	// LIMIT FOR ALL KM MUATAN EXCEPT KM 1
	if (isset($getdataFromStreet)) {
		$datafixlimitperkmallmuatan = array();
		for ($i=0; $i < sizeof($getdataFromStreet); $i++) {
			$streetremovecoma         = explode(",", $getdataFromStreet[$i]['street_name']);
				if (!in_array($streetremovecoma[0], $arraynotin)) {
					$streetfix                = str_replace(" ", "_", $streetremovecoma[0]);

					$middlelimitname          = $streetfix.$postfix_middle_limit_allkmmuatan;
					$toplimitname             = $streetfix.$postfix_top_limit_allkmmuatan;

					$getMapSettingByLimitName = $this->m_poipoolmaster->getThisMapSettingByLimitName($middlelimitname, $toplimitname);

					if (sizeof($getMapSettingByLimitName) > 1) {
							array_push($datafixlimitperkmallmuatan, array(
								"street_id"               => $getdataFromStreet[$i]['street_id'],
								"street_name"             => $getdataFromStreet[$i]['street_name'],
								"mapsetting_type"         => 1,
								"mapsetting_name_alias"   => $streetremovecoma[0],
								"mapsetting_name"         => $streetfix,
								"mapsetting_middle_limit" => $getMapSettingByLimitName[0]['mapsetting_limit_value'],
								"mapsetting_top_limit"    => $getMapSettingByLimitName[1]['mapsetting_limit_value']
							));
					}else {
						array_push($datafixlimitperkmallmuatan, array(
							"street_id"               => $getdataFromStreet[$i]['street_id'],
							"street_name"             => $getdataFromStreet[$i]['street_name'],
							"mapsetting_type"         => 1,
							"mapsetting_name_alias"   => $streetremovecoma[0],
							"mapsetting_name"         => $streetfix,
							"mapsetting_middle_limit" => 0,
							"mapsetting_top_limit"    => 0
						));
					}
				}
		}
	}

	// LIMIT ALL KOSONGAN
	if (isset($getdataFromStreet)) {
		$datafixlimitperkmallkosongan = array();
		for ($i=0; $i < sizeof($getdataFromStreet); $i++) {
			$streetremovecoma         = explode(",", $getdataFromStreet[$i]['street_name']);
				if (!in_array($streetremovecoma[0], $arraynotinAllKMKosongan)) {
					$streetfix                = str_replace(" ", "_", $streetremovecoma[0]);

					$middlelimitname          = $streetfix.$postfix_middle_limit_allkmkosongan;
					$toplimitname             = $streetfix.$postfix_top_limit_allkmkosongan;

					$getMapSettingByLimitName = $this->m_poipoolmaster->getThisMapSettingByLimitName($middlelimitname, $toplimitname);

					if (sizeof($getMapSettingByLimitName) > 1) {
							array_push($datafixlimitperkmallkosongan, array(
								"street_id"               => $getdataFromStreet[$i]['street_id'],
								"street_name"             => $getdataFromStreet[$i]['street_name'],
								"mapsetting_type"         => 1,
								"mapsetting_name_alias"   => $streetremovecoma[0],
								"mapsetting_name"         => $streetfix,
								"mapsetting_middle_limit" => $getMapSettingByLimitName[0]['mapsetting_limit_value'],
								"mapsetting_top_limit"    => $getMapSettingByLimitName[1]['mapsetting_limit_value']
							));
					}else {
						array_push($datafixlimitperkmallkosongan, array(
							"street_id"               => $getdataFromStreet[$i]['street_id'],
							"street_name"             => $getdataFromStreet[$i]['street_name'],
							"mapsetting_type"         => 1,
							"mapsetting_name_alias"   => $streetremovecoma[0],
							"mapsetting_name"         => $streetfix,
							"mapsetting_middle_limit" => 0,
							"mapsetting_top_limit"    => 0
						));
					}
				}
		}
	}

	// GET LIST IN ROM & PORT
	$romType 		         = 3; //ROM TYPE
	$portType 		       = 4; //PORT TYPE
	$portTypeCPBIB 		   = 7; //portType CP BIB
	$portTypeANTBIR 		 = 8; //portType ANT BIR
	$romStreet           = $this->m_poipoolmaster->getstreet_now2($romType);
	$portStreet          = $this->m_poipoolmaster->getstreet_now2($portType);
	$portCPBIB           = $this->m_poipoolmaster->getstreet_now2($portTypeCPBIB);
	$portANTBIR          = $this->m_poipoolmaster->getstreet_now2($portTypeANTBIR);
	$dataRomFix          = array();
	$dataPortFix         = array();
	$dataPortCPBIBFix    = array();
	$dataPortANTBIRFix   = array();

	// echo "<pre>";
	// var_dump($romStreet);die();
	// echo "<pre>";

	for ($j=0; $j < sizeof($romStreet); $j++) {
		$street_name_rom                  = explode(",", $romStreet[$j]['street_name']);
		$street_nameromfix                = $street_name_rom[0];
		$dataStateRom[$street_nameromfix] = 0;

		for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
			$autocheck          = json_decode($masterdatavehicle[$i]['vehicle_autocheck']);
			$auto_last_position = explode(",", $autocheck->auto_last_position);
			$datalastposition   = $auto_last_position[0];
			$auto_status 		    = $autocheck->auto_status;

			if ($auto_status != "M") {
				if ($datalastposition == $street_nameromfix) {
						$dataStateRom[$street_nameromfix] += 1;
				}
			}
		}
	}

	for ($k=0; $k < sizeof($portStreet); $k++) {
		$street_name_port                   = explode(",", $portStreet[$k]['street_name']);
		$street_nameportfix                 = $street_name_port[0];
		$dataStatePort[$street_nameportfix] = 0;

		for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
			$autocheck          = json_decode($masterdatavehicle[$i]['vehicle_autocheck']);
			$auto_last_position = explode(",", $autocheck->auto_last_position);
			$datalastposition   = $auto_last_position[0];
			$auto_status 		    = $autocheck->auto_status;

			if ($auto_status != "M") {
				if ($datalastposition == $street_nameportfix) {
						$dataStatePort[$street_nameportfix] += 1;
				}
			}
		}
	}

	for ($l=0; $l < sizeof($portCPBIB); $l++) {
		$street_name_portCPBIB                   = explode(",", $portCPBIB[$l]['street_name']);
		$street_nameportCPBIBfix                 = $street_name_portCPBIB[0];
		$dataStatePortCPBIB[$street_nameportCPBIBfix] = 0;

		for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
			$autocheck          = json_decode($masterdatavehicle[$i]['vehicle_autocheck']);
			$auto_last_position = explode(",", $autocheck->auto_last_position);
			$datalastposition   = $auto_last_position[0];
			$auto_status 		    = $autocheck->auto_status;

			if ($auto_status != "M") {
				if ($datalastposition == $street_nameportCPBIBfix) {
						$dataStatePortCPBIB[$street_nameportCPBIBfix] += 1;
				}
			}
		}
	}

	for ($m=0; $m < sizeof($portANTBIR); $m++) {
		$street_name_portANTBIR                   = explode(",", $portANTBIR[$m]['street_name']);
		$street_nameportANTBIRfix                 = $street_name_portANTBIR[0];
		$dataStatePortANTBIR[$street_nameportANTBIRfix] = 0;

		for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
			$autocheck          = json_decode($masterdatavehicle[$i]['vehicle_autocheck']);
			$auto_last_position = explode(",", $autocheck->auto_last_position);
			$datalastposition   = $auto_last_position[0];
			$auto_status 		    = $autocheck->auto_status;

			if ($auto_status != "M") {
				if ($datalastposition == $street_nameportANTBIRfix) {
						$dataStatePortANTBIR[$street_nameportANTBIRfix] += 1;
				}
			}
		}
	}

	// echo "<pre>";
	// var_dump($dataJumlahInKmMuatan);die(); //dataJumlahInKmKosongan dataJumlahInKmMuatan
	// echo "<pre>";
	// LIMIT SETTING PER KM

	// echo "<pre>";
	// var_dump($dataStatePort);die();
	// echo "<pre>";

	echo json_encode(array("msg" => "success", "code" => 200, "dataPortCPBIB" => $dataStatePortCPBIB, "dataPortANTBIR" => $dataStatePortANTBIR, "dataRominQuickCount" => $dataStateRom, "dataPortinQuickCount" => $dataStatePort, "dataMuatan" => $dataJumlahInKmMuatan, "dataKosongan" => $dataJumlahInKmKosongan, "dataMuatan2" => $dataJumlahInKmMuatan_2, "dataKosongan2" => $dataJumlahInKmKosongan_2, "lastcheck" => $lasttimecheck, "datafixlimitperkmallmuatan" => $datafixlimitperkmallmuatan, "datafixlimitperkmallkosongan" => $datafixlimitperkmallkosongan, "datafixlimitkm1muatan" => $datafixlimitkm1muatan));
}

function getstreetautomaticbycompanyidadminonly(){
	$typeofstreet 		 = $_POST['typeofstreet'];
	$companyid 		 	   = $_POST['companyid'];
	$masterdatavehicle = $this->m_poipoolmaster->getmastervehiclebycontractor($companyid);
	$allStreet         = $this->m_poipoolmaster->getstreet_now($typeofstreet);
	$dataRomFix        = array();
	$lasttimecheck 		 = date("d-m-Y H:i:s", strtotime("+1 hour"));

	for ($j=0; $j < sizeof($allStreet); $j++) {
		$street_name                = explode(",", $allStreet[$j]['street_name']);
		$street_namefix             = $street_name[0];
		$dataState[$street_namefix] = 0;

		for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
			$autocheck          = json_decode($masterdatavehicle[$i]['vehicle_autocheck']);
			$auto_last_position = explode(",", $autocheck->auto_last_position);
			$datalastposition   = $auto_last_position[0];
			$auto_status 		    = $autocheck->auto_status;

			if ($auto_status != "M") {
				if ($datalastposition == $street_namefix) {
						$dataState[$street_namefix] += 1;
				}
			}
		}
	}

	// LIMIT SETTING PER KM
	$getdataFromStreet = $this->m_poipoolmaster->getstreet_now($typeofstreet);
	$mapSettingType    = $this->m_poipoolmaster->getMapSettingByType($typeofstreet);

	$postfix_middle_limit = "_middle_limit";
	$postfix_top_limit    = "_top_limit";

	if (isset($getdataFromStreet)) {
		$datafixlimitperkm = array();
		for ($i=0; $i < sizeof($getdataFromStreet); $i++) {
			$streetremovecoma         = explode(",", $getdataFromStreet[$i]['street_name']);
			$streetfix                = str_replace(" ", "_", $streetremovecoma[0]);

			$middlelimitname          = $streetfix.$postfix_middle_limit;
			$toplimitname             = $streetfix.$postfix_top_limit;

			$getMapSettingByLimitName = $this->m_poipoolmaster->getThisMapSettingByLimitName($middlelimitname, $toplimitname);

			if (sizeof($getMapSettingByLimitName) > 1) {
					array_push($datafixlimitperkm, array(
						"street_id"               => $getdataFromStreet[$i]['street_id'],
						"street_name"             => $getdataFromStreet[$i]['street_name'],
						"mapsetting_type"         => 1,
						"mapsetting_name_alias"   => $streetremovecoma[0],
						"mapsetting_name"         => $streetfix,
						"mapsetting_middle_limit" => $getMapSettingByLimitName[0]['mapsetting_limit_value'],
						"mapsetting_top_limit"    => $getMapSettingByLimitName[1]['mapsetting_limit_value']
					));
			}else {
				array_push($datafixlimitperkm, array(
					"street_id"               => $getdataFromStreet[$i]['street_id'],
					"street_name"             => $getdataFromStreet[$i]['street_name'],
					"mapsetting_type"         => 1,
					"mapsetting_name_alias"   => $streetremovecoma[0],
					"mapsetting_name"         => $streetfix,
					"mapsetting_middle_limit" => 0,
					"mapsetting_top_limit"    => 0
				));
			}
		}
	}

	// echo "<pre>";
	// var_dump($dataState);die();
	// echo "<pre>";

	echo json_encode(array("msg" => "success", "code" => 200, "data" => $dataState, "allstreet" => $allStreet, "lastcheck" => $lasttimecheck, "datafixlimit" => $datafixlimitperkm));
}

// FUNCTION KHUSUS JALUR TIA START
	// KHUSUS UNIT START
	function quickcounttiaunit(){
		// echo "<pre>";
		// var_dump("quickcounttiaunit");die();
		// echo "<pre>";
			ini_set('max_execution_time', '300');
			set_time_limit(300);
			if (! isset($this->sess->user_type))
			{
				redirect(base_url());
			}

			$user_id       = $this->sess->user_id;
			$user_parent   = $this->sess->user_parent;
			$privilegecode = $this->sess->user_id_role;
			$user_company  = $this->sess->user_company;

			if($privilegecode == 0){
				$user_id_fix = $user_id;
			}elseif ($privilegecode == 1) {
				$user_id_fix = $user_parent;
			}elseif ($privilegecode == 2) {
				$user_id_fix = $user_parent;
			}elseif ($privilegecode == 3) {
				$user_id_fix = $user_parent;
			}elseif ($privilegecode == 4) {
				$user_id_fix = $user_parent;
			}elseif ($privilegecode == 5) {
				$user_id_fix = $user_id;
			}elseif ($privilegecode == 6) {
				$user_id_fix = $user_id;
			}else{
				$user_id_fix = $user_id;
			}

			$companyid                       = $this->sess->user_company;
			$user_dblive                     = $this->sess->user_dblive;
			$mastervehicle                   = $this->m_poipoolmaster->getmastervehicleforheatmap();

			$datafix                         = array();
			$deviceidygtidakada              = array();
			$statusvehicle['engine_on']  = 0;
			$statusvehicle['engine_off'] = 0;

			for ($i=0; $i < sizeof($mastervehicle); $i++) {
				$jsonautocheck = json_decode($mastervehicle[$i]['vehicle_autocheck']);
				if (isset($jsonautocheck->auto_status)) {
					// code...
				$auto_status   = $jsonautocheck->auto_status;

				if ($privilegecode == 5 || $privilegecode == 6) {
					if ($mastervehicle[$i]['vehicle_company'] == $user_company) {
						if ($jsonautocheck->auto_last_engine == "ON") {
							$statusvehicle['engine_on'] += 1;
						}else {
							$statusvehicle['engine_off'] += 1;
						}
					}
				}else {
					if ($jsonautocheck->auto_last_engine == "ON") {
						$statusvehicle['engine_on'] += 1;
					}else {
						$statusvehicle['engine_off'] += 1;
					}
				}

					if ($auto_status != "M") {
						array_push($datafix, array(
							"vehicle_id"             => $mastervehicle[$i]['vehicle_id'],
							"vehicle_user_id"        => $mastervehicle[$i]['vehicle_user_id'],
							"vehicle_company"        => $mastervehicle[$i]['vehicle_company'],
							"vehicle_device"         => $mastervehicle[$i]['vehicle_device'],
							"vehicle_no"             => $mastervehicle[$i]['vehicle_no'],
							"vehicle_name"           => $mastervehicle[$i]['vehicle_name'],
							"vehicle_active_date2"   => $mastervehicle[$i]['vehicle_active_date2'],
							"auto_last_lat"          => substr($jsonautocheck->auto_last_lat, 0, 10),
							"auto_last_long"         => substr($jsonautocheck->auto_last_long, 0, 10),
						));
					}
				}
			}

			$company                  = $this->dashboardmodel->getcompany_byowner($privilegecode);
				if ($company) {

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
				}else {
					$this->params['company']   = 0;
					$this->params['companyid'] = 0;
					$this->params['vehicle']   = 0;
				}

			// echo "<pre>";
			// var_dump($company);die();
			// echo "<pre>";


			$this->params['url_code_view']  = "1";
			$this->params['code_view_menu'] = "monitor";
			$this->params['maps_code']      = "morehundred";

			$this->params['engine_on']      = $statusvehicle['engine_on'];
			$this->params['engine_off']     = $statusvehicle['engine_off'];


			$rstatus                        = $this->dashboardmodel->gettotalstatus($this->sess->user_id);

			$datastatus                     = explode("|", $rstatus);
			$this->params['total_online']   = $datastatus[0]+$datastatus[1]; //p + K
			$this->params['total_vehicle']  = $datastatus[3];
			$this->params['total_offline']  = $datastatus[2];

			$this->params['vehicledata']  = $datafix;
			$this->params['vehicletotal'] = sizeof($mastervehicle);
			$this->params['poolmaster']   = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");
			$getvehicle_byowner           = $this->dashboardmodel->getvehicle_byownerforheatmap();
			// echo "<pre>";
			// var_dump($getvehicle_byowner);die();
			// echo "<pre>";
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
			$this->params['mapsetting']     = $this->m_poipoolmaster->getmapsetting();
			$this->params['poolmaster']     = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");

			// echo "<pre>";
			// var_dump($this->params['mapsetting']);die();
			// echo "<pre>";

			$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
			$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

				if ($privilegecode == 1) {
					$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
					$this->params["content"]        = $this->load->view('newdashboard/quickcount/v_home_tiavehicle', $this->params, true);
					$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
				}elseif ($privilegecode == 2) {
					$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
					$this->params["content"]        = $this->load->view('newdashboard/quickcount/v_home_tiavehicle', $this->params, true);
					$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
				}elseif ($privilegecode == 3) {
					$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
					$this->params["content"]        = $this->load->view('newdashboard/quickcount/v_home_tiavehicle', $this->params, true);
					$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
				}elseif ($privilegecode == 4) {
					$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
					$this->params["content"]        = $this->load->view('newdashboard/quickcount/v_home_tiavehicle', $this->params, true);
					$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
				}elseif ($privilegecode == 5) {
					$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
					$this->params["content"]        = $this->load->view('newdashboard/quickcount/v_home_tiavehicle', $this->params, true);
					$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
				}elseif ($privilegecode == 6) {
					$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
					$this->params["content"]        = $this->load->view('newdashboard/quickcount/v_home_tiavehicle', $this->params, true);
					$this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
				}else {
					$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
					$this->params["content"]        = $this->load->view('newdashboard/quickcount/v_home_tiavehicle', $this->params, true);
					$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
				}
	}

	function km_quickcount_newtia(){
		$companyid 				 = $_POST['companyid'];
		$masterdatavehicle = $this->m_poipoolmaster->getmastervehiclebycontractor($companyid);
		$dataKmMuatanFix   = array();
		$dataKmKosonganFix = array();

		// echo "<pre>";
		// var_dump($masterdatavehicle);die();
		// echo "<pre>";

		$dataJumlahInKmKosongan_2['gb0_port_bib_kosongan_1']    = 0;
		$dataJumlahInKmKosongan_2['gb1_port_bib_kosongan_2']    = 0;
		$dataJumlahInKmKosongan_2['gb2_port_bir_kosongan_1']    = 0;
		$dataJumlahInKmKosongan_2['gb3_port_bir_kosongan_2']    = 0;
		$dataJumlahInKmKosongan_2['gb4_simpang_bayah_kosongan'] = 0;

		$dataJumlahInKmMuatan_2['gb5_port_bib_antrian']         = 0;
		$dataJumlahInKmMuatan_2['gb6_port_bir_antrian_wb']      = 0;

		$dataJumlahInKmMuatan['KM_0']  = 0;
		$dataJumlahInKmMuatan['KM_1']  = 0;
		$dataJumlahInKmMuatan['KM_2']  = 0;
		$dataJumlahInKmMuatan['KM_3']  = 0;
		$dataJumlahInKmMuatan['KM_4']  = 0;
		$dataJumlahInKmMuatan['KM_5']  = 0;
		$dataJumlahInKmMuatan['KM_6']  = 0;
		$dataJumlahInKmMuatan['KM_7']  = 0;
		$dataJumlahInKmMuatan['KM_8']  = 0;
		$dataJumlahInKmMuatan['KM_9']  = 0;
		$dataJumlahInKmMuatan['KM_10'] = 0;
		$dataJumlahInKmMuatan['KM_11'] = 0;
		$dataJumlahInKmMuatan['KM_12'] = 0;
		$dataJumlahInKmMuatan['KM_13'] = 0;
		$dataJumlahInKmMuatan['KM_14'] = 0;
		$dataJumlahInKmMuatan['KM_15'] = 0;
		$dataJumlahInKmMuatan['KM_16'] = 0;
		$dataJumlahInKmMuatan['KM_17'] = 0;
		$dataJumlahInKmMuatan['KM_18'] = 0;
		$dataJumlahInKmMuatan['KM_19'] = 0;
		$dataJumlahInKmMuatan['KM_20'] = 0;
		$dataJumlahInKmMuatan['KM_21'] = 0;
		$dataJumlahInKmMuatan['KM_22'] = 0;
		$dataJumlahInKmMuatan['KM_23'] = 0;
		$dataJumlahInKmMuatan['KM_24'] = 0;
		$dataJumlahInKmMuatan['KM_25'] = 0;
		$dataJumlahInKmMuatan['KM_26'] = 0;
		$dataJumlahInKmMuatan['KM_27'] = 0;
		$dataJumlahInKmMuatan['KM_28'] = 0;
		$dataJumlahInKmMuatan['KM_29'] = 0;
		$dataJumlahInKmMuatan['KM_30'] = 0;

		$dataJumlahInKmKosongan['KM_0']  = 0;
		$dataJumlahInKmKosongan['KM_1']  = 0;
		$dataJumlahInKmKosongan['KM_2']  = 0;
		$dataJumlahInKmKosongan['KM_3']  = 0;
		$dataJumlahInKmKosongan['KM_4']  = 0;
		$dataJumlahInKmKosongan['KM_5']  = 0;
		$dataJumlahInKmKosongan['KM_6']  = 0;
		$dataJumlahInKmKosongan['KM_7']  = 0;
		$dataJumlahInKmKosongan['KM_8']  = 0;
		$dataJumlahInKmKosongan['KM_9']  = 0;
		$dataJumlahInKmKosongan['KM_10'] = 0;
		$dataJumlahInKmKosongan['KM_11'] = 0;
		$dataJumlahInKmKosongan['KM_12'] = 0;
		$dataJumlahInKmKosongan['KM_13'] = 0;
		$dataJumlahInKmKosongan['KM_14'] = 0;
		$dataJumlahInKmKosongan['KM_15'] = 0;
		$dataJumlahInKmKosongan['KM_16'] = 0;
		$dataJumlahInKmKosongan['KM_17'] = 0;
		$dataJumlahInKmKosongan['KM_18'] = 0;
		$dataJumlahInKmKosongan['KM_19'] = 0;
		$dataJumlahInKmKosongan['KM_20'] = 0;
		$dataJumlahInKmKosongan['KM_21'] = 0;
		$dataJumlahInKmKosongan['KM_22'] = 0;
		$dataJumlahInKmKosongan['KM_23'] = 0;
		$dataJumlahInKmKosongan['KM_24'] = 0;
		$dataJumlahInKmKosongan['KM_25'] = 0;
		$dataJumlahInKmKosongan['KM_26'] = 0;
		$dataJumlahInKmKosongan['KM_27'] = 0;
		$dataJumlahInKmKosongan['KM_28'] = 0;
		$dataJumlahInKmKosongan['KM_29'] = 0;
		$dataJumlahInKmKosongan['KM_30'] = 0;

		$lasttimecheck = date("d-m-Y H:i:s", strtotime("+1 hour"));

		for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
			$autocheck          = json_decode($masterdatavehicle[$i]['vehicle_autocheck']);
			$auto_last_position = explode(",", $autocheck->auto_last_position);
			$jalur_name         = $autocheck->auto_last_road;
			$datalastposition   = $auto_last_position[0];
			$auto_status        = $autocheck->auto_status;

			if ($auto_status != "M") {
				if ($jalur_name == "kosongan") {
					if ($datalastposition == "TIA KM 0" || $datalastposition == "TIA KM 0.5") {
						$dataJumlahInKmKosongan['KM_1'] += 1;
					}elseif ($datalastposition == "TIA KM 1" || $datalastposition == "TIA KM 1.5") {
						$dataJumlahInKmKosongan['KM_2'] += 1;
					}elseif ($datalastposition == "TIA KM 2" || $datalastposition == "TIA KM 2.5") {
						$dataJumlahInKmKosongan['KM_3'] += 1;
					}elseif ($datalastposition == "TIA KM 3" || $datalastposition == "TIA KM 3.5") {
						$dataJumlahInKmKosongan['KM_4'] += 1;
					}elseif ($datalastposition == "TIA KM 4" || $datalastposition == "TIA KM 4.5") {
						$dataJumlahInKmKosongan['KM_5'] += 1;
					}elseif ($datalastposition == "TIA KM 5" || $datalastposition == "TIA KM 5.5") {
						$dataJumlahInKmKosongan['KM_6'] += 1;
					}elseif ($datalastposition == "TIA KM 6" || $datalastposition == "TIA KM 6.5") {
						$dataJumlahInKmKosongan['KM_7'] += 1;
					}elseif ($datalastposition == "TIA KM 7" || $datalastposition == "TIA KM 7.5") {
						$dataJumlahInKmKosongan['KM_8'] += 1;
					}elseif ($datalastposition == "TIA KM 8" || $datalastposition == "TIA KM 8.5") {
						$dataJumlahInKmKosongan['KM_9'] += 1;
					}elseif ($datalastposition == "TIA KM 9" || $datalastposition == "TIA KM 9.5") {
						$dataJumlahInKmKosongan['KM_10'] += 1;
					}elseif ($datalastposition == "TIA KM 10" || $datalastposition == "TIA KM 10.5") {
						$dataJumlahInKmKosongan['KM_11'] += 1;
					}elseif ($datalastposition == "TIA KM 11" || $datalastposition == "TIA KM 11.5") {
						$dataJumlahInKmKosongan['KM_12'] += 1;
					}elseif ($datalastposition == "TIA KM 12" || $datalastposition == "TIA KM 12.5") {
						$dataJumlahInKmKosongan['KM_13'] += 1;
					}elseif ($datalastposition == "TIA KM 13" || $datalastposition == "TIA KM 13.5") {
						$dataJumlahInKmKosongan['KM_14'] += 1;
					}elseif ($datalastposition == "TIA KM 14" || $datalastposition == "TIA KM 14.5") {
						$dataJumlahInKmKosongan['KM_15'] += 1;
					}elseif ($datalastposition == "TIA KM 15" || $datalastposition == "TIA KM 15.5") {
						$dataJumlahInKmKosongan['KM_16'] += 1;
					}elseif ($datalastposition == "TIA KM 16" || $datalastposition == "TIA KM 16.5") {
						$dataJumlahInKmKosongan['KM_17'] += 1;
					}elseif ($datalastposition == "TIA KM 17" || $datalastposition == "TIA KM 17.5") {
						$dataJumlahInKmKosongan['KM_18'] += 1;
					}elseif ($datalastposition == "TIA KM 18" || $datalastposition == "TIA KM 18.5") {
						$dataJumlahInKmKosongan['KM_19'] += 1;
					}elseif ($datalastposition == "TIA KM 19" || $datalastposition == "TIA KM 19.5") {
						$dataJumlahInKmKosongan['KM_20'] += 1;
					}elseif ($datalastposition == "TIA KM 20" || $datalastposition == "TIA KM 20.5") {
						$dataJumlahInKmKosongan['KM_21'] += 1;
					}elseif ($datalastposition == "TIA KM 21" || $datalastposition == "TIA KM 21.5") {
						$dataJumlahInKmKosongan['KM_22'] += 1;
					}elseif ($datalastposition == "TIA KM 22" || $datalastposition == "TIA KM 22.5") {
						$dataJumlahInKmKosongan['KM_23'] += 1;
					}elseif ($datalastposition == "TIA KM 23" || $datalastposition == "TIA KM 23.5") {
						$dataJumlahInKmKosongan['KM_24'] += 1;
					}elseif ($datalastposition == "TIA KM 24" || $datalastposition == "TIA KM 24.5") {
						$dataJumlahInKmKosongan['KM_25'] += 1;
					}elseif ($datalastposition == "TIA KM 25" || $datalastposition == "TIA KM 25.5") {
						$dataJumlahInKmKosongan['KM_26'] += 1;
					}elseif ($datalastposition == "TIA KM 26" || $datalastposition == "TIA KM 26.5") {
						$dataJumlahInKmKosongan['KM_27'] += 1;
					}elseif ($datalastposition == "TIA KM 27" || $datalastposition == "TIA KM 27.5") {
						$dataJumlahInKmKosongan['KM_28'] += 1;
					}elseif ($datalastposition == "TIA KM 28" || $datalastposition == "TIA KM 28.5") {
						$dataJumlahInKmKosongan['KM_29'] += 1;
					}elseif ($datalastposition == "TIA KM 29" || $datalastposition == "TIA KM 29.5") {
						$dataJumlahInKmKosongan['KM_30'] += 1;
					}
				}else {
					if ($datalastposition == "TIA KM 0" || $datalastposition == "TIA KM 0.5") {
						$dataJumlahInKmMuatan['KM_1'] += 1;
					}elseif ($datalastposition == "TIA KM 1" || $datalastposition == "TIA KM 1.5") {
						$dataJumlahInKmMuatan['KM_2'] += 1;
					}elseif ($datalastposition == "TIA KM 2" || $datalastposition == "TIA KM 2.5") {
						$dataJumlahInKmMuatan['KM_3'] += 1;
					}elseif ($datalastposition == "TIA KM 3" || $datalastposition == "TIA KM 3.5") {
						$dataJumlahInKmMuatan['KM_4'] += 1;
					}elseif ($datalastposition == "TIA KM 4" || $datalastposition == "TIA KM 4.5") {
						$dataJumlahInKmMuatan['KM_5'] += 1;
					}elseif ($datalastposition == "TIA KM 5" || $datalastposition == "TIA KM 5.5") {
						$dataJumlahInKmMuatan['KM_6'] += 1;
					}elseif ($datalastposition == "TIA KM 6" || $datalastposition == "TIA KM 6.5") {
						$dataJumlahInKmMuatan['KM_7'] += 1;
					}elseif ($datalastposition == "TIA KM 7" || $datalastposition == "TIA KM 7.5") {
						$dataJumlahInKmMuatan['KM_8'] += 1;
					}elseif ($datalastposition == "TIA KM 8" || $datalastposition == "TIA KM 8.5") {
						$dataJumlahInKmMuatan['KM_9'] += 1;
					}elseif ($datalastposition == "TIA KM 9" || $datalastposition == "TIA KM 9.5") {
						$dataJumlahInKmMuatan['KM_10'] += 1;
					}elseif ($datalastposition == "TIA KM 10" || $datalastposition == "TIA KM 10.5") {
						$dataJumlahInKmMuatan['KM_11'] += 1;
					}elseif ($datalastposition == "TIA KM 11" || $datalastposition == "TIA KM 11.5") {
						$dataJumlahInKmMuatan['KM_12'] += 1;
					}elseif ($datalastposition == "TIA KM 12" || $datalastposition == "TIA KM 12.5") {
						$dataJumlahInKmMuatan['KM_13'] += 1;
					}elseif ($datalastposition == "TIA KM 13" || $datalastposition == "TIA KM 13.5") {
						$dataJumlahInKmMuatan['KM_14'] += 1;
					}elseif ($datalastposition == "TIA KM 14" || $datalastposition == "TIA KM 14.5") {
						$dataJumlahInKmMuatan['KM_15'] += 1;
					}elseif ($datalastposition == "TIA KM 15" || $datalastposition == "TIA KM 15.5") {
						$dataJumlahInKmMuatan['KM_16'] += 1;
					}elseif ($datalastposition == "TIA KM 16" || $datalastposition == "TIA KM 16.5") {
						$dataJumlahInKmMuatan['KM_17'] += 1;
					}elseif ($datalastposition == "TIA KM 17" || $datalastposition == "TIA KM 17.5") {
						$dataJumlahInKmMuatan['KM_18'] += 1;
					}elseif ($datalastposition == "TIA KM 18" || $datalastposition == "TIA KM 18.5") {
						$dataJumlahInKmMuatan['KM_19'] += 1;
					}elseif ($datalastposition == "TIA KM 19" || $datalastposition == "TIA KM 19.5") {
						$dataJumlahInKmMuatan['KM_20'] += 1;
					}elseif ($datalastposition == "TIA KM 20" || $datalastposition == "TIA KM 20.5") {
						$dataJumlahInKmMuatan['KM_21'] += 1;
					}elseif ($datalastposition == "TIA KM 21" || $datalastposition == "TIA KM 21.5") {
						$dataJumlahInKmMuatan['KM_22'] += 1;
					}elseif ($datalastposition == "TIA KM 22" || $datalastposition == "TIA KM 22.5") {
						$dataJumlahInKmMuatan['KM_23'] += 1;
					}elseif ($datalastposition == "TIA KM 23" || $datalastposition == "TIA KM 23.5") {
						$dataJumlahInKmMuatan['KM_24'] += 1;
					}elseif ($datalastposition == "TIA KM 24" || $datalastposition == "TIA KM 24.5") {
						$dataJumlahInKmMuatan['KM_25'] += 1;
					}elseif ($datalastposition == "TIA KM 25" || $datalastposition == "TIA KM 25.5") {
						$dataJumlahInKmMuatan['KM_26'] += 1;
					}elseif ($datalastposition == "TIA KM 26" || $datalastposition == "TIA KM 26.5") {
						$dataJumlahInKmMuatan['KM_27'] += 1;
					}elseif ($datalastposition == "TIA KM 27" || $datalastposition == "TIA KM 27.5") {
						$dataJumlahInKmMuatan['KM_28'] += 1;
					}elseif ($datalastposition == "TIA KM 28" || $datalastposition == "TIA KM 28.5") {
						$dataJumlahInKmMuatan['KM_29'] += 1;
					}elseif ($datalastposition == "TIA KM 29" || $datalastposition == "KM 29.5" || $datalastposition == "KM 30" || $datalastposition == "KM 30.5") {
						$dataJumlahInKmMuatan['KM_30'] += 1;
					}
				}
			}
		}

		// LIMIT SETTING PER KM
		$arraynotin              = array("Port BIR - Kosongan 2", "Port BIB - Kosongan 2", "Simpang Bayah - Kosongan", "Port BIR - Antrian WB", "Port BIB - Kosongan 1",
																			"Port BIB - Antrian", "Port BIR - Kosongan 1", "KM 1");

		$arraynotinAllKMKosongan = array("Port BIR - Kosongan 2", "Port BIB - Kosongan 2", "Simpang Bayah - Kosongan", "Port BIR - Antrian WB", "Port BIB - Kosongan 1",
																			"Port BIB - Antrian", "Port BIR - Kosongan 1");
	  $arrayinKM1Muatan	 			 = array("KM 1");

		$getdataFromStreet = $this->m_poipoolmaster->getstreet_now(1);
		$mapSettingType    = $this->m_poipoolmaster->getMapSettingByType(1);

		$postfix_middle_limit_allkmmuatan   = "_middle_limit_allkmmuatan";
		$postfix_top_limit_allkmmuatan      = "_top_limit_allkmmuatan";
		$postfix_middle_limit_allkmkosongan = "_middle_limit_allkmkosongan";
		$postfix_top_limit_allkmkosongan    = "_top_limit_allkmkosongan";
		$postfix_bottom_limit_km1muatan     = "_bottom_limit_km1muatan";
		$postfix_middle_limit_km1muatan     = "_middle_limit_km1muatan";
		$postfix_top_limit_km1muatan        = "_top_limit_km1muatan";

		// LIMIT ONLY KM 1
		if (isset($getdataFromStreet)) {
			$datafixlimitkm1muatan = array();
			for ($i=0; $i < sizeof($getdataFromStreet); $i++) {
				$streetremovecoma         = explode(",", $getdataFromStreet[$i]['street_name']);
					if (in_array($streetremovecoma[0], $arrayinKM1Muatan)) {
						$streetfix                = str_replace(" ", "_", $streetremovecoma[0]);
						$bottomlimitname          = $streetfix.$postfix_bottom_limit_km1muatan;
						$middlelimitname          = $streetfix.$postfix_middle_limit_km1muatan;
						$toplimitname             = $streetfix.$postfix_top_limit_km1muatan;
						$getMapSettingByLimitName = $this->m_poipoolmaster->getThisMapSettingByLimitName_mapsetting_onlykm1($bottomlimitname, $middlelimitname, $toplimitname);

						// echo "<pre>";
						// var_dump($getMapSettingByLimitName);die();
						// echo "<pre>";

						if (sizeof($getMapSettingByLimitName) > 1) {
								array_push($datafixlimitkm1muatan, array(
									"street_id"               => $getdataFromStreet[$i]['street_id'],
									"street_name"             => $getdataFromStreet[$i]['street_name'],
									"mapsetting_type"         => 1,
									"mapsetting_name_alias"   => $streetremovecoma[0],
									"mapsetting_name"         => $streetfix,
									"mapsetting_bottom_limit" => $getMapSettingByLimitName[0]['mapsetting_limit_value'],
									"mapsetting_middle_limit" => $getMapSettingByLimitName[1]['mapsetting_limit_value'],
									"mapsetting_top_limit"    => $getMapSettingByLimitName[2]['mapsetting_limit_value']
								));
						}else {
							array_push($datafixlimitkm1muatan, array(
								"street_id"               => $getdataFromStreet[$i]['street_id'],
								"street_name"             => $getdataFromStreet[$i]['street_name'],
								"mapsetting_type"         => 1,
								"mapsetting_name_alias"   => $streetremovecoma[0],
								"mapsetting_name"         => $streetfix,
								"mapsetting_bottom_limit" => 0,
								"mapsetting_middle_limit" => 0,
								"mapsetting_top_limit"    => 0
							));
						}
					}
			}
		}

		// LIMIT FOR ALL KM MUATAN EXCEPT KM 1
		if (isset($getdataFromStreet)) {
			$datafixlimitperkmallmuatan = array();
			for ($i=0; $i < sizeof($getdataFromStreet); $i++) {
				$streetremovecoma         = explode(",", $getdataFromStreet[$i]['street_name']);
					if (!in_array($streetremovecoma[0], $arraynotin)) {
						$streetfix                = str_replace(" ", "_", $streetremovecoma[0]);

						$middlelimitname          = $streetfix.$postfix_middle_limit_allkmmuatan;
						$toplimitname             = $streetfix.$postfix_top_limit_allkmmuatan;

						$getMapSettingByLimitName = $this->m_poipoolmaster->getThisMapSettingByLimitName($middlelimitname, $toplimitname);

						if (sizeof($getMapSettingByLimitName) > 1) {
								array_push($datafixlimitperkmallmuatan, array(
									"street_id"               => $getdataFromStreet[$i]['street_id'],
									"street_name"             => $getdataFromStreet[$i]['street_name'],
									"mapsetting_type"         => 1,
									"mapsetting_name_alias"   => $streetremovecoma[0],
									"mapsetting_name"         => $streetfix,
									"mapsetting_middle_limit" => $getMapSettingByLimitName[0]['mapsetting_limit_value'],
									"mapsetting_top_limit"    => $getMapSettingByLimitName[1]['mapsetting_limit_value']
								));
						}else {
							array_push($datafixlimitperkmallmuatan, array(
								"street_id"               => $getdataFromStreet[$i]['street_id'],
								"street_name"             => $getdataFromStreet[$i]['street_name'],
								"mapsetting_type"         => 1,
								"mapsetting_name_alias"   => $streetremovecoma[0],
								"mapsetting_name"         => $streetfix,
								"mapsetting_middle_limit" => 0,
								"mapsetting_top_limit"    => 0
							));
						}
					}
			}
		}

		// LIMIT ALL KOSONGAN
		if (isset($getdataFromStreet)) {
			$datafixlimitperkmallkosongan = array();
			for ($i=0; $i < sizeof($getdataFromStreet); $i++) {
				$streetremovecoma         = explode(",", $getdataFromStreet[$i]['street_name']);
					if (!in_array($streetremovecoma[0], $arraynotinAllKMKosongan)) {
						$streetfix                = str_replace(" ", "_", $streetremovecoma[0]);

						$middlelimitname          = $streetfix.$postfix_middle_limit_allkmkosongan;
						$toplimitname             = $streetfix.$postfix_top_limit_allkmkosongan;

						$getMapSettingByLimitName = $this->m_poipoolmaster->getThisMapSettingByLimitName($middlelimitname, $toplimitname);

						if (sizeof($getMapSettingByLimitName) > 1) {
								array_push($datafixlimitperkmallkosongan, array(
									"street_id"               => $getdataFromStreet[$i]['street_id'],
									"street_name"             => $getdataFromStreet[$i]['street_name'],
									"mapsetting_type"         => 1,
									"mapsetting_name_alias"   => $streetremovecoma[0],
									"mapsetting_name"         => $streetfix,
									"mapsetting_middle_limit" => $getMapSettingByLimitName[0]['mapsetting_limit_value'],
									"mapsetting_top_limit"    => $getMapSettingByLimitName[1]['mapsetting_limit_value']
								));
						}else {
							array_push($datafixlimitperkmallkosongan, array(
								"street_id"               => $getdataFromStreet[$i]['street_id'],
								"street_name"             => $getdataFromStreet[$i]['street_name'],
								"mapsetting_type"         => 1,
								"mapsetting_name_alias"   => $streetremovecoma[0],
								"mapsetting_name"         => $streetfix,
								"mapsetting_middle_limit" => 0,
								"mapsetting_top_limit"    => 0
							));
						}
					}
			}
		}

		// GET LIST IN ROM & PORT
		$romType 		         = 3; //ROM TYPE
		$portType 		       = 4; //PORT TYPE
		$portTypeCPBIB 		   = 7; //portType CP BIB
		$portTypeANTBIR 		 = 8; //portType ANT BIR
		$romStreet           = $this->m_poipoolmaster->getstreet_now2($romType);
		$portStreet          = $this->m_poipoolmaster->getstreet_now2($portType);
		$portCPBIB           = $this->m_poipoolmaster->getstreet_now2($portTypeCPBIB);
		$portANTBIR          = $this->m_poipoolmaster->getstreet_now2($portTypeANTBIR);
		$dataRomFix          = array();
		$dataPortFix         = array();
		$dataPortCPBIBFix    = array();
		$dataPortANTBIRFix   = array();

		// echo "<pre>";
		// var_dump($romStreet);die();
		// echo "<pre>";

		for ($j=0; $j < sizeof($romStreet); $j++) {
			$street_name_rom                  = explode(",", $romStreet[$j]['street_name']);
			$street_nameromfix                = $street_name_rom[0];
			$dataStateRom[$street_nameromfix] = 0;

			for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
				$autocheck          = json_decode($masterdatavehicle[$i]['vehicle_autocheck']);
				$auto_last_position = explode(",", $autocheck->auto_last_position);
				$datalastposition   = $auto_last_position[0];
				$auto_status 		    = $autocheck->auto_status;

				if ($auto_status != "M") {
					if ($datalastposition == $street_nameromfix) {
							$dataStateRom[$street_nameromfix] += 1;
					}
				}
			}
		}

		for ($k=0; $k < sizeof($portStreet); $k++) {
			$street_name_port                   = explode(",", $portStreet[$k]['street_name']);
			$street_nameportfix                 = $street_name_port[0];
			$dataStatePort[$street_nameportfix] = 0;

			for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
				$autocheck          = json_decode($masterdatavehicle[$i]['vehicle_autocheck']);
				$auto_last_position = explode(",", $autocheck->auto_last_position);
				$datalastposition   = $auto_last_position[0];
				$auto_status 		    = $autocheck->auto_status;

				if ($auto_status != "M") {
					if ($datalastposition == $street_nameportfix) {
							$dataStatePort[$street_nameportfix] += 1;
					}
				}
			}
		}

		for ($l=0; $l < sizeof($portCPBIB); $l++) {
			$street_name_portCPBIB                   = explode(",", $portCPBIB[$l]['street_name']);
			$street_nameportCPBIBfix                 = $street_name_portCPBIB[0];
			$dataStatePortCPBIB[$street_nameportCPBIBfix] = 0;

			for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
				$autocheck          = json_decode($masterdatavehicle[$i]['vehicle_autocheck']);
				$auto_last_position = explode(",", $autocheck->auto_last_position);
				$datalastposition   = $auto_last_position[0];
				$auto_status 		    = $autocheck->auto_status;

				if ($auto_status != "M") {
					if ($datalastposition == $street_nameportCPBIBfix) {
							$dataStatePortCPBIB[$street_nameportCPBIBfix] += 1;
					}
				}
			}
		}

		for ($m=0; $m < sizeof($portANTBIR); $m++) {
			$street_name_portANTBIR                   = explode(",", $portANTBIR[$m]['street_name']);
			$street_nameportANTBIRfix                 = $street_name_portANTBIR[0];
			$dataStatePortANTBIR[$street_nameportANTBIRfix] = 0;

			for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
				$autocheck          = json_decode($masterdatavehicle[$i]['vehicle_autocheck']);
				$auto_last_position = explode(",", $autocheck->auto_last_position);
				$datalastposition   = $auto_last_position[0];
				$auto_status 		    = $autocheck->auto_status;

				if ($auto_status != "M") {
					if ($datalastposition == $street_nameportANTBIRfix) {
							$dataStatePortANTBIR[$street_nameportANTBIRfix] += 1;
					}
				}
			}
		}

		// echo "<pre>";
		// var_dump($dataJumlahInKmMuatan);die(); //dataJumlahInKmKosongan dataJumlahInKmMuatan
		// echo "<pre>";
		// LIMIT SETTING PER KM

		// echo "<pre>";
		// var_dump($dataStatePort);die();
		// echo "<pre>";

		echo json_encode(array("msg" => "success", "code" => 200, "dataPortCPBIB" => $dataStatePortCPBIB, "dataPortANTBIR" => $dataStatePortANTBIR, "dataRominQuickCount" => $dataStateRom, "dataPortinQuickCount" => $dataStatePort, "dataMuatan" => $dataJumlahInKmMuatan, "dataKosongan" => $dataJumlahInKmKosongan, "dataMuatan2" => $dataJumlahInKmMuatan_2, "dataKosongan2" => $dataJumlahInKmKosongan_2, "lastcheck" => $lasttimecheck, "datafixlimitperkmallmuatan" => $datafixlimitperkmallmuatan, "datafixlimitperkmallkosongan" => $datafixlimitperkmallkosongan, "datafixlimitkm1muatan" => $datafixlimitkm1muatan));
	}

	function getlistinkmtia(){
		$dataVehicleOnKosongan     = array();
		$dataVehicleOnMuatan       = array();
		$idkm 							       = $_POST['idkm'];
		$contractor 							 = $_POST['contractor'];
		$kmonsearch 					     = array();
		$masterdatavehicle         = $this->m_poipoolmaster->getmastervehiclebycontractor($contractor);

		if ($idkm == 1) {
			$kmonsearch = array("TIA KM 0", "TIA KM 0.5");
		}elseif ($idkm == 2) {
			$kmonsearch = array("TIA KM 1", "TIA KM 1.5",);
		}elseif ($idkm == 3) {
			$kmonsearch = array("TIA KM 2", "TIA KM 2.5");
		}elseif ($idkm == 4) {
			$kmonsearch = array("TIA KM 3", "TIA KM 3.5");
		}elseif ($idkm == 5) {
			$kmonsearch = array("TIA KM 4", "TIA KM 4.5");
		}elseif ($idkm == 6) {
			$kmonsearch = array("TIA KM 5", "TIA KM 5.5");
		}elseif ($idkm == 7) {
			$kmonsearch = array("TIA KM 6", "TIA KM 6.5");
		}elseif ($idkm == 8) {
			$kmonsearch = array("TIA KM 7", "TIA KM 7.5");
		}elseif ($idkm == 9) {
			$kmonsearch = array("TIA KM 8", "TIA KM 8.5");
		}elseif ($idkm == 10) {
			$kmonsearch = array("TIA KM 9", "TIA KM 9.5");
		}elseif ($idkm == 11) {
			$kmonsearch = array("TIA KM 10", "TIA KM 10.5");
		}elseif ($idkm == 12) {
			$kmonsearch = array("TIA KM 11", "TIA KM 11.5");
		}elseif ($idkm == 13) {
			$kmonsearch = array("TIA KM 12", "TIA KM 12.5");
		}elseif ($idkm == 14) {
			$kmonsearch = array("TIA KM 13", "TIA KM 13.5");
		}elseif ($idkm == 15) {
			$kmonsearch = array("TIA KM 14", "TIA KM 14.5");
		}elseif ($idkm == 16) {
			$kmonsearch = array("TIA KM 15", "TIA KM 15.5");
		}elseif ($idkm == 17) {
			$kmonsearch = array("TIA KM 16", "TIA KM 16.5");
		}elseif ($idkm == 18) {
			$kmonsearch = array("TIA KM 17", "TIA KM 17.5");
		}elseif ($idkm == 19) {
			$kmonsearch = array("TIA KM 18", "TIA KM 18.5");
		}elseif ($idkm == 20) {
			$kmonsearch = array("TIA KM 19", "TIA KM 19.5");
		}elseif ($idkm == 21) {
			$kmonsearch = array("TIA KM 20", "TIA KM 20.5");
		}elseif ($idkm == 22) {
			$kmonsearch = array("TIA KM 21", "TIA KM 21.5");
		}elseif ($idkm == 23) {
			$kmonsearch = array("TIA KM 22", "TIA KM 22.5");
		}elseif ($idkm == 24) {
			$kmonsearch = array("TIA KM 23", "TIA KM 23.5");
		}elseif ($idkm == 25) {
			$kmonsearch = array("TIA KM 24", "TIA KM 24.5");
		}elseif ($idkm == 26) {
			$kmonsearch = array("TIA KM 25", "TIA KM 25.5");
		}elseif ($idkm == 27) {
			$kmonsearch = array("TIA KM 26", "TIA KM 26.5");
		}elseif ($idkm == 28) {
			$kmonsearch = array("TIA KM 27", "TIA KM 27.5");
		}elseif ($idkm == 29) {
			$kmonsearch = array("TIA KM 28", "TIA KM 28.5");
		}elseif ($idkm == 30) {
			$kmonsearch = array("TIA KM 29", "TIA KM 29.5", "TIA KM 30", "TIA KM 30.5");
		}

		$vCompanyFix 					 = "KM " . $idkm;

		// echo "<pre>";
		// var_dump($vCompany);die();
		// echo "<pre>";

		for ($i=0; $i < sizeof($masterdatavehicle); $i++) {
			$autocheck          = json_decode($masterdatavehicle[$i]['vehicle_autocheck']);
			$jalur_name         = $autocheck->auto_last_road;
			$auto_last_position = explode(",", $autocheck->auto_last_position);
			$datalastposition   = $auto_last_position[0];

			// echo "<pre>";
			// var_dump($datalastposition);die();
			// echo "<pre>";

				if (in_array($datalastposition, $kmonsearch)) {
					if ($jalur_name == "kosongan") {
						array_push($dataVehicleOnKosongan, array(
							"vehicle_no"            => $masterdatavehicle[$i]['vehicle_no'],
							"vehicle_name"          => $masterdatavehicle[$i]['vehicle_name'],
							"auto_last_lat"         => $autocheck->auto_last_lat,
							"auto_last_long"        => $autocheck->auto_last_long,
							"auto_last_positionfix" => $datalastposition,
							"auto_last_engine"      => $autocheck->auto_last_engine,
							"auto_last_speed"       => $autocheck->auto_last_speed,
							"auto_last_update"      => date("d-m-Y H:i:s", strtotime($autocheck->auto_last_update))
						));
					}else {
						array_push($dataVehicleOnMuatan, array(
							"vehicle_no"            => $masterdatavehicle[$i]['vehicle_no'],
							"vehicle_name"          => $masterdatavehicle[$i]['vehicle_name'],
							"auto_last_lat"         => $autocheck->auto_last_lat,
							"auto_last_long"        => $autocheck->auto_last_long,
							"auto_last_positionfix" => $datalastposition,
							"auto_last_engine"      => $autocheck->auto_last_engine,
							"auto_last_speed"       => $autocheck->auto_last_speed,
							"auto_last_update"      => date("d-m-Y H:i:s", strtotime($autocheck->auto_last_update))
						));
					}
				}
		}

		// echo "<pre>";
		// var_dump($dataVehicleOnKosongan);die();
		// echo "<pre>";

		echo json_encode(array("msg" => "success", "code" => 200, "dataKosongan" => $dataVehicleOnKosongan, "dataMuatan" => $dataVehicleOnMuatan, "kmsent" => $vCompanyFix));
	}
	// KHUSUS UNIT END
// FUNCTION KHUSUS JALUR TIA END

// MONITORING KHUSUS VEHICLE BIB START
function vehicletrackingbib(){
	ini_set('max_execution_time', '300');
	set_time_limit(300);
	if (! isset($this->sess->user_type))
	{
		redirect(base_url());
	}

	$user_id       = $this->sess->user_id;
	$user_parent   = $this->sess->user_parent;
	$privilegecode = $this->sess->user_id_role;
	$user_company  = $this->sess->user_company;

	if($privilegecode == 0){
		$user_id_fix = $user_id;
	}elseif ($privilegecode == 1) {
		$user_id_fix = $user_parent;
	}elseif ($privilegecode == 2) {
		$user_id_fix = $user_parent;
	}elseif ($privilegecode == 3) {
		$user_id_fix = $user_parent;
	}elseif ($privilegecode == 4) {
		$user_id_fix = $user_parent;
	}elseif ($privilegecode == 5) {
		$user_id_fix = $user_id;
	}elseif ($privilegecode == 6) {
		$user_id_fix = $user_id;
	}else{
		$user_id_fix = $user_id;
	}

	$companyid                       = $this->sess->user_company;
	$user_dblive                     = $this->sess->user_dblive;
	$mastervehicle                   = $this->m_poipoolmaster->getmastervehicleforbibvehicle();

	// echo "<pre>";
	// var_dump($mastervehicle);die();
	// echo "<pre>";

	$datafix                         = array();
	$deviceidygtidakada              = array();
	$statusvehicle['engine_on']  = 0;
	$statusvehicle['engine_off'] = 0;

	for ($i=0; $i < sizeof($mastervehicle); $i++) {
		$jsonautocheck = json_decode($mastervehicle[$i]['vehicle_autocheck']);
		if (isset($jsonautocheck->auto_status)) {
			// code...
		$auto_status   = $jsonautocheck->auto_status;

		if ($privilegecode == 5 || $privilegecode == 6) {
			if ($mastervehicle[$i]['vehicle_company'] == $user_company) {
				if ($jsonautocheck->auto_last_engine == "ON") {
					$statusvehicle['engine_on'] += 1;
				}else {
					$statusvehicle['engine_off'] += 1;
				}
			}
		}else {
			if ($jsonautocheck->auto_last_engine == "ON") {
				$statusvehicle['engine_on'] += 1;
			}else {
				$statusvehicle['engine_off'] += 1;
			}
		}

			if ($auto_status != "M") {
				array_push($datafix, array(
					"vehicle_id"             => $mastervehicle[$i]['vehicle_id'],
					"vehicle_user_id"        => $mastervehicle[$i]['vehicle_user_id'],
					"vehicle_company"        => $mastervehicle[$i]['vehicle_company'],
					"vehicle_device"         => $mastervehicle[$i]['vehicle_device'],
					"vehicle_no"             => $mastervehicle[$i]['vehicle_no'],
					"vehicle_name"           => $mastervehicle[$i]['vehicle_name'],
					"vehicle_active_date2"   => $mastervehicle[$i]['vehicle_active_date2'],
					"auto_last_lat"          => substr($jsonautocheck->auto_last_lat, 0, 10),
					"auto_last_long"         => substr($jsonautocheck->auto_last_long, 0, 10),
				));
			}
		}
	}

	$company                  = $this->dashboardmodel->getcompany_byowner($privilegecode);
		if ($company) {

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
		}else {
			$this->params['company']   = 0;
			$this->params['companyid'] = 0;
			$this->params['vehicle']   = 0;
		}

	// echo "<pre>";
	// var_dump($company);die();
	// echo "<pre>";


	$this->params['url_code_view']  = "1";
	$this->params['code_view_menu'] = "monitoring_lv";
	$this->params['maps_code']      = "morehundred";

	$this->params['engine_on']      = $statusvehicle['engine_on'];
	$this->params['engine_off']     = $statusvehicle['engine_off'];


	$rstatus                        = $this->dashboardmodel->gettotalstatus($this->sess->user_id);

	$datastatus                     = explode("|", $rstatus);
	$this->params['total_online']   = $datastatus[0]+$datastatus[1]; //p + K
	$this->params['total_vehicle']  = $datastatus[3];
	$this->params['total_offline']  = $datastatus[2];

	$this->params['vehicledata']  = $datafix;
	$this->params['vehicletotal'] = sizeof($mastervehicle);
	$this->params['poolmaster']   = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");
	$getvehicle_byowner           = $mastervehicle;
	// echo "<pre>";
	// var_dump($getvehicle_byowner);die();
	// echo "<pre>";
	$totalmobilnya                = sizeof($getvehicle_byowner);
	if ($totalmobilnya == 0) {
		$this->params['name']         = "0";
		$this->params['host']         = "0";
	}else {
		$arr          = explode("@", $getvehicle_byowner[0]['vehicle_device']);
		$this->params['name']         = $arr[0];
		$this->params['host']         = $arr[1];
	}

	$this->params['resultactive']   = $this->dashboardmodel->vehicleactive();
	$this->params['resultexpired']  = $this->dashboardmodel->vehicleexpired();
	$this->params['resulttotaldev'] = $this->dashboardmodel->totaldevice();
	$this->params['mapsetting']     = $this->m_poipoolmaster->getmapsetting();
	$this->params['poolmaster']     = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");

	// echo "<pre>";
	// var_dump($this->params['mapsetting']);die();
	// echo "<pre>";

	$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
	$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/trackers/vehicle/v_tracking_vehicle', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		}elseif ($privilegecode == 2) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/trackers/vehicle/v_tracking_vehicle', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
		}elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/trackers/vehicle/v_tracking_vehicle', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		}elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/trackers/vehicle/v_tracking_vehicle', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		}else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/trackers/vehicle/v_tracking_vehicle', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
}

function getdatacontractorvehiclebib(){
	$user_id      = $this->sess->user_id;
	$user_company = $this->sess->user_company;
	$user_parent  = $this->sess->user_parent;
	$user_id_role = $this->sess->user_id_role;

		if ($user_id_role == 0) {
			$this->db->where("company_created_by", $user_id);
		}elseif ($user_id_role == 1) {
			$this->db->where("company_created_by", $user_parent);
		}elseif ($user_id_role == 2) {
			$this->db->where("company_created_by", $user_parent);
		}elseif ($user_id_role == 3) {
			$this->db->where("company_created_by", $user_parent);
		}elseif ($user_id_role == 4) {
			$this->db->where("company_created_by", $user_parent);
		}elseif ($user_id_role == 5) {
			$this->db->where("company_id", $user_company);
		}elseif ($user_id_role == 6) {
			$this->db->where("company_id", $user_company);
		}

	$this->db->where("company_flag", 0);
	$this->db->where("company_exca", 3);
	$this->db->order_by("company_name", "ASC");
	$q     = $this->db->get("company");
	$rows  = $q->result_array();

	// echo "<pre>";
	// var_dump($rows);die();
	// echo "<pre>";

	echo json_encode(array("msg" => "success", "code" => 200, "data" => $rows));
}

function getvehiclebycontractorvehiclebib(){
	$user_id       = $this->sess->user_id;
	$user_parent   = $this->sess->user_parent;
	$privilegecode = $this->sess->user_id_role;
	$user_company  = $this->sess->user_company;
	$companyid     = $this->input->post('companyid');

	$this->db->select("*");

		if ($companyid == 0) {
			if ($privilegecode == 0) {
				$this->db->where("vehicle_user_id", $user_id);
			}elseif ($privilegecode == 1) {
				$this->db->where("vehicle_user_id", $user_parent);
			}elseif ($privilegecode == 2) {
				$this->db->where("vehicle_user_id", $user_parent);
			}elseif ($privilegecode == 3) {
				$this->db->where("vehicle_user_id", $user_parent);
			}elseif ($privilegecode == 4) {
				$this->db->where("vehicle_user_id", $user_parent);
			}elseif ($privilegecode == 5) {
				$this->db->where("vehicle_company", $user_company);
			}elseif ($privilegecode == 6) {
				$this->db->where("vehicle_company", $user_company);
			}
		}else {
			$this->db->where("vehicle_company", $companyid);
		}

	$this->db->where("vehicle_typeunit", 2);
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

function mapsstandardvehiclebib(){
	if (! isset($this->sess->user_type))
	{
		redirect(base_url());
	}

	$user_id       = $this->sess->user_id;
	$user_parent   = $this->sess->user_parent;
	$privilegecode = $this->sess->user_id_role;

	if($privilegecode == 0){
		$user_id_fix = $user_id;
	}elseif ($privilegecode == 1) {
		$user_id_fix = $user_parent;
	}elseif ($privilegecode == 2) {
		$user_id_fix = $user_parent;
	}elseif ($privilegecode == 3) {
		$user_id_fix = $user_parent;
	}elseif ($privilegecode == 4) {
		$user_id_fix = $user_parent;
	}elseif ($privilegecode == 5) {
		$user_id_fix = $user_id;
	}elseif ($privilegecode == 6) {
		$user_id_fix = $user_id;
	}else{
		$user_id_fix = $user_id;
	}

	$companyid                       = $this->sess->user_company;
	$user_dblive                     = $this->sess->user_dblive;
	$companyid 											 = $_POST['companyid'];
	$forclearmaps                    = $this->m_poipoolmaster->getmastervehiclebib();
	$mastervehicle                   = $this->m_poipoolmaster->getmastervehiclebibbycontractor($companyid);

	$datafix                         = array();
	$deviceidygtidakada              = array();

	for ($i=0; $i < sizeof($mastervehicle); $i++) {
		$jsonautocheck = json_decode($mastervehicle[$i]['vehicle_autocheck']);
		$auto_status = $jsonautocheck->auto_status;

			if ($auto_status != "M") {
				array_push($datafix, array(
					"vehicle_id"           => $mastervehicle[$i]['vehicle_id'],
					"vehicle_user_id"      => $mastervehicle[$i]['vehicle_user_id'],
					"vehicle_device"       => $mastervehicle[$i]['vehicle_device'],
					"vehicle_no"           => $mastervehicle[$i]['vehicle_no'],
					"vehicle_name"         => $mastervehicle[$i]['vehicle_name'],
					"vehicle_active_date2" => $mastervehicle[$i]['vehicle_active_date2'],
					"auto_last_lat"        => substr($jsonautocheck->auto_last_lat, 0, 10),
					"auto_last_long"       => substr($jsonautocheck->auto_last_long, 0, 10),
					"auto_last_road"       => $jsonautocheck->auto_last_road,
					"auto_last_engine"     => $jsonautocheck->auto_last_engine,
					"auto_last_speed"      => $jsonautocheck->auto_last_speed,
					"auto_last_course"     => $jsonautocheck->auto_last_course,
				));
			}
	}

	// echo "<pre>";
	// var_dump(sizeof($datafix));die();
	// echo "<pre>";

	echo json_encode(array("code" => "success", "msg" => "success", "data" => $datafix, "alldataforclearmaps" => $forclearmaps));
}

function vehiclebibByContractor(){
	$user_id         = $this->sess->user_id;
	$user_parent     = $this->sess->user_parent;
	$privilegecode   = $this->sess->user_id_role;
	$user_company    = $this->sess->user_company;
	$companyid       = $this->input->post('companyid');
	$valueMapsOption = $this->input->post('valuemapsoption');

	$this->db->select("*");
		if ($companyid == 0) {
			if ($privilegecode == 0) {
				$this->db->where("vehicle_user_id", $user_id);
			}elseif ($privilegecode == 1) {
				$this->db->where("vehicle_user_id", $user_parent);
			}elseif ($privilegecode == 2) {
				$this->db->where("vehicle_user_id", $user_parent);
			}elseif ($privilegecode == 3) {
				$this->db->where("vehicle_user_id", $user_parent);
			}elseif ($privilegecode == 4) {
				$this->db->where("vehicle_user_id", $user_parent);
			}elseif ($privilegecode == 5) {
				$this->db->where("vehicle_company", $user_company);
			}elseif ($privilegecode == 6) {
				$this->db->where("vehicle_company", $user_company);
			}
		}else {
			$this->db->where("vehicle_company", $companyid);
		}

	$this->db->where("vehicle_typeunit", 2);
	$this->db->where("vehicle_status <>", 3);
	$this->db->where("vehicle_gotohistory", 0);
	$this->db->where("vehicle_autocheck is not NULL");
	$this->db->order_by("vehicle_no", "ASC");
	$q    = $this->db->get("vehicle");
	$rows = $q->result_array();

	if ($valueMapsOption == 1) {
		$poolmaster        = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");
		$datavehicle       = array();

			for ($i=0; $i < sizeof($rows); $i++) {
				$autocheck         = json_decode($rows[$i]['vehicle_autocheck']);
						array_push($datavehicle, array(
							"vehicle_id"     => $rows[$i]['vehicle_id'],
							"vehicle_no"     => $rows[$i]['vehicle_no'],
							"vehicle_name"   => $rows[$i]['vehicle_name'],
							"vehicle_device" => $rows[$i]['vehicle_device'],
							"auto_last_lat"  => $autocheck->auto_last_lat,
							"auto_last_long" => $autocheck->auto_last_long
						));
			}
			echo json_encode(array("msg" => "success", "code" => 200, "data" => $rows, "datavehicle" => $datavehicle, "poolmaster" => $poolmaster));
	}else {
		echo json_encode(array("msg" => "success", "code" => 200, "data" => $rows));
	}

	// echo "<pre>";
	// var_dump($datavehicle);die();
	// echo "<pre>";

}

function vehiclebibByContractorheatmap(){
	$user_id         = $this->sess->user_id;
	$user_parent     = $this->sess->user_parent;
	$privilegecode   = $this->sess->user_id_role;
	$user_company    = $this->sess->user_company;
	$companyid       = $this->input->post('companyid');
	$valueMapsOption = $this->input->post('valuemapsoption');

	$this->db->select("*");
		if ($companyid == 0) {
			if ($privilegecode == 0) {
				$this->db->where("vehicle_user_id", $user_id);
			}elseif ($privilegecode == 1) {
				$this->db->where("vehicle_user_id", $user_parent);
			}elseif ($privilegecode == 2) {
				$this->db->where("vehicle_user_id", $user_parent);
			}elseif ($privilegecode == 3) {
				$this->db->where("vehicle_user_id", $user_parent);
			}elseif ($privilegecode == 4) {
				$this->db->where("vehicle_user_id", $user_parent);
			}elseif ($privilegecode == 5) {
				$this->db->where("vehicle_user_id", 4408);
			}elseif ($privilegecode == 6) {
				$this->db->where("vehicle_user_id", 4408);
			}
		}else {
			$this->db->where("vehicle_company", $companyid);
		}

	$this->db->where("vehicle_typeunit", 2);
	$this->db->where("vehicle_status <>", 3);
	$this->db->where("vehicle_gotohistory", 0);
	$this->db->where("vehicle_autocheck is not NULL");
	$this->db->order_by("vehicle_no", "ASC");
	$q    = $this->db->get("vehicle");
	$rows = $q->result_array();

	if ($valueMapsOption == 1) {
		$poolmaster        = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");
		$datavehicle       = array();

			for ($i=0; $i < sizeof($rows); $i++) {
				$autocheck         = json_decode($rows[$i]['vehicle_autocheck']);
						array_push($datavehicle, array(
							"vehicle_id"      => $rows[$i]['vehicle_id'],
							"vehicle_no"      => $rows[$i]['vehicle_no'],
							"vehicle_name"    => $rows[$i]['vehicle_name'],
							"vehicle_device"  => $rows[$i]['vehicle_device'],
							"vehicle_company" => $rows[$i]['vehicle_company'],
							"auto_last_lat"   => $autocheck->auto_last_lat,
							"auto_last_long"  => $autocheck->auto_last_long
						));
			}
			echo json_encode(array("msg" => "success", "code" => 200, "data" => $rows, "datavehicle" => $datavehicle, "poolmaster" => $poolmaster));
	}else {
		echo json_encode(array("msg" => "success", "code" => 200, "data" => $rows));
	}

	// echo "<pre>";
	// var_dump($datavehicle);die();
	// echo "<pre>";

}

function getdetailbydevidvehiclebib(){
	if (! isset($this->sess->user_type))
	{
		redirect(base_url());
	}

	$user_dblive     = $this->sess->user_dblive;
	$device_id       = $_POST['device_id'];
	$device          = explode("@", $_POST['device_id']);
	$device0         = $device[0];
	$device1         = $device[1];

	$mastervehicle   = $this->m_poipoolmaster->getmastervehiclebibbydevid($device_id);
	// echo "<pre>";
	// var_dump($mastervehicle);die();
	// echo "<pre>";

	$getdatalastinfo = $this->m_poipoolmaster->getLastPosition("webtracking_gps", $mastervehicle[0]['vehicle_dbname_live'], $device0);
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

// MONITORING KHUSUS VEHICLE BIB END

// HRM VIEW START
	function mapsstandardhrm(){
		ini_set('max_execution_time', '300');
		set_time_limit(300);
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$user_id       = $this->sess->user_id;
		$user_parent   = $this->sess->user_parent;
		$privilegecode = $this->sess->user_id_role;
		$user_company  = $this->sess->user_company;

		if($privilegecode == 0){
			$user_id_fix = $user_id;
		}elseif ($privilegecode == 1) {
			$user_id_fix = $user_parent;
		}elseif ($privilegecode == 2) {
			$user_id_fix = $user_parent;
		}elseif ($privilegecode == 3) {
			$user_id_fix = $user_parent;
		}elseif ($privilegecode == 4) {
			$user_id_fix = $user_parent;
		}elseif ($privilegecode == 5) {
			$user_id_fix = $user_id;
		}elseif ($privilegecode == 6) {
			$user_id_fix = $user_id;
		}else{
			$user_id_fix = $user_id;
		}

		$companyid                       = $this->sess->user_company;
		$user_dblive                     = $this->sess->user_dblive;
		$mastervehicle                   = $this->m_poipoolmaster->getmastervehicleHRM();

		$datafix                         = array();
		$deviceidygtidakada              = array();
		$statusvehicle['engine_on']  = 0;
		$statusvehicle['engine_off'] = 0;

		for ($i=0; $i < sizeof($mastervehicle); $i++) {
			$jsonautocheck = json_decode($mastervehicle[$i]['vehicle_autocheck']);
			if (isset($jsonautocheck->auto_status)) {
				// code...
			$auto_status   = $jsonautocheck->auto_status;

			if ($privilegecode == 5 || $privilegecode == 6) {
				if ($mastervehicle[$i]['vehicle_company'] == $user_company) {
					if ($jsonautocheck->auto_last_engine == "ON") {
						$statusvehicle['engine_on'] += 1;
					}else {
						$statusvehicle['engine_off'] += 1;
					}
				}
			}else {
				if ($jsonautocheck->auto_last_engine == "ON") {
					$statusvehicle['engine_on'] += 1;
				}else {
					$statusvehicle['engine_off'] += 1;
				}
			}

				if ($auto_status != "M") {
					array_push($datafix, array(
						"vehicle_id"             => $mastervehicle[$i]['vehicle_id'],
						"vehicle_user_id"        => $mastervehicle[$i]['vehicle_user_id'],
						"vehicle_company"        => $mastervehicle[$i]['vehicle_company'],
						"vehicle_device"         => $mastervehicle[$i]['vehicle_device'],
						"vehicle_no"             => $mastervehicle[$i]['vehicle_no'],
						"vehicle_name"           => $mastervehicle[$i]['vehicle_name'],
						"vehicle_active_date2"   => $mastervehicle[$i]['vehicle_active_date2'],
						"auto_last_lat"          => substr($jsonautocheck->auto_last_lat, 0, 10),
						"auto_last_long"         => substr($jsonautocheck->auto_last_long, 0, 10),
					));
				}
			}
		}

		$company                  = $this->dashboardmodel->getcompany_byowner($privilegecode);
			if ($company) {

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
			}else {
				$this->params['company']   = 0;
				$this->params['companyid'] = 0;
				$this->params['vehicle']   = 0;
			}

		// echo "<pre>";
		// var_dump($datafix);die();
		// echo "<pre>";


		$this->params['url_code_view']  = "1";
		$this->params['code_view_menu'] = "monitoring_hrm";
		$this->params['maps_code']      = "morehundred";

		$this->params['engine_on']      = $statusvehicle['engine_on'];
		$this->params['engine_off']     = $statusvehicle['engine_off'];


		$rstatus                        = $this->dashboardmodel->gettotalstatus($this->sess->user_id);

		$datastatus                     = explode("|", $rstatus);
		$this->params['total_online']   = $datastatus[0]+$datastatus[1]; //p + K
		$this->params['total_vehicle']  = $datastatus[3];
		$this->params['total_offline']  = $datastatus[2];

		$this->params['vehicledata']  = $datafix;
		$this->params['vehicletotal'] = sizeof($mastervehicle);
		$this->params['poolmaster']   = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");
		$getvehicle_byowner           = $this->dashboardmodel->getvehicle_byownerforheatmap();
		// echo "<pre>";
		// var_dump($getvehicle_byowner);die();
		// echo "<pre>";
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
		$this->params['mapsetting']     = $this->m_poipoolmaster->getmapsetting();
		$this->params['poolmaster']     = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster");

		// echo "<pre>";
		// var_dump($this->params['mapsetting']);die();
		// echo "<pre>";

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

			if ($privilegecode == 1) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/trackers/hrm/v_home_maps', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
			}elseif ($privilegecode == 2) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/trackers/hrm/v_home_maps', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
			}elseif ($privilegecode == 3) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/trackers/hrm/v_home_maps', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
			}elseif ($privilegecode == 4) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/trackers/hrm/v_home_maps', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
			}else {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/trackers/hrm/v_home_maps', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
			}
	}
// HRM VIEW END
























}
