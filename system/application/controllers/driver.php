<?php
include "base.php";

class Driver extends Base {

	function Driver()
	{
		parent::Base();
		$this->load->helper('common_helper');
		$this->load->helper('email');
		$this->load->library('email');
		$this->load->model("dashboardmodel");
		$this->load->helper('common');
		$this->load->model("driver_model");
		$this->load->model("gpsmodel");
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

	}

	function index()
	{
		ini_set('display_errors', 1);
		
		
		$iddriver_simper = $this->getDriver_idsimper($this->sess->user_login);
		if(count($iddriver_simper)>0){
			redirect(base_url()."driver/profile/".$iddriver_simper->driver_id);
			
		}
		
		
		$this->dbtransporter = $this->load->database('transporter', true);
		$driver_company      = $this->sess->user_company;
		$driver_group        = $this->sess->user_group;
		$vehicle_user_id     = $this->sess->user_id;
		$user_parent         = $this->sess->user_parent;
		$privilegecode       = $this->sess->user_id_role;
		$datavehicle         = $this->driver_model->getalldatabyuserid("webtracking_vehicle", "vehicle_user_id", $vehicle_user_id);

		//ssi company
		if($this->sess->user_company == 356)
		{
			$row_vehicle = $this->get_vehicle();
		}

		if($this->sess->user_group == 0){
			$this->dbtransporter->where("driver_company", $driver_company);
		}else
		{
			$this->dbtransporter->where("driver_group", $driver_group);
		}

		$this->dbtransporter->where("driver_status", 1);
		$this->dbtransporter->orderby("driver_name","asc");
		$qtotal = $this->dbtransporter->get("driver");
		$rows   = $qtotal->result();

		// GET ASSIGNED VEHICLE STATUS
		$total                        = count($rows);
		$config['total_rows']         = $total;
		$this->params["title"]        = "Manage Driver";
		$this->params["total"]        = $total;
		$this->params["data"]         = $rows;
		$this->params["row2"] 				= $datavehicle;

		// echo "<pre>";
		// var_dump($this->params["data"]);die();
		// echo "<pre>";
		//ssi company
		if($this->sess->user_company == 356)
		{
			$this->params["car"] = $row_vehicle;
		}

		$this->params['code_view_menu'] = "masterdata";
		$this->params['title']          = "Driver";

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

		if ($privilegecode == 9) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_driver', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/driver/v_driver', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new_driver", $this->params);
		}else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/driver/v_driver', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
	}

	function get_vehicle()
	{
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_company", $this->sess->user_company);
		$qvehicle = $this->db->get("vehicle");
		$row_vehicle = $qvehicle->result();
		return $row_vehicle;
	}

	function get_vehicle_bymv03($imei)
	{
		$this->db->select("vehicle_id,vehicle_device,vehicle_mv03,vehicle_no");
		$this->db->order_by("vehicle_id", "desc");
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_mv03", $imei);
		$this->db->limit(1);
		$qvehicle = $this->db->get("vehicle");
		$row_vehicle = $qvehicle->row();

		return $row_vehicle;
	}

	function save() {
	if (! isset($this->sess->user_company)) {
		redirect(base_url());
	}

	$this->dbtransporter 		= $this->load->database("transporter", true);

	$driver_company         = isset($_POST['driver_company']) ? $_POST['driver_company']:                 0;
	$driver_name            = isset($_POST['driver_name']) ? $_POST['driver_name']:                       "";
	$driver_address         = isset($_POST['driver_address']) ? $_POST['driver_address']:                 "";
	$driver_phone           = isset($_POST['driver_phone']) ? $_POST['driver_phone']:                     0;
	$driver_mobile          = isset($_POST['driver_mobile']) ? $_POST['driver_mobile']:                   0;
	$driver_mobile2         = isset($_POST['driver_mobile2']) ? $_POST['driver_mobile2']:                 0;
	$driver_licence         = isset($_POST['driver_licence']) ? $_POST['driver_licence']:                 "";
	$driver_licence_no      = isset($_POST['driver_licence_no']) ? $_POST['driver_licence_no']:           "";
	$driver_sex             = isset($_POST['driver_sex']) ? $_POST['driver_sex']:                         "";
	$driver_joint_date      = isset($_POST['driver_joint_date']) ? $_POST['driver_joint_date']:           "";
	$driver_note            = isset($_POST['driver_note']) ? $_POST['driver_note']:                       "";
	$driver_rfid            = isset($_POST['driver_note']) ? $_POST['driver_rfid']:                       "";
	$driver_licence_expired = isset($_POST['driver_licence_expired']) ? $_POST['driver_licence_expired']: "";
	$driver_siof            = isset($_POST['driver_siof']) ? $_POST['driver_siof']:                       "";
	$driver_siof_expired    = isset($_POST['driver_siof_expired']) ? $_POST['driver_siof_expired']:       "";
	$driver_group           = isset($_POST['driver_group']) ? $_POST['driver_group']:                     "";
	$driver_idcard          = isset($_POST['driver_idcard']) ? $_POST['driver_idcard']:                   "";

	$error = "";
	unset($data);
	$data['driver_company']         = $driver_company;
	$data['driver_name']            = $driver_name;
	$data['driver_address']         = $driver_address;
	$data['driver_phone']           = $driver_phone;
	$data['driver_mobile']          = $driver_mobile;
	$data['driver_mobile2']         = $driver_mobile2;
	$data['driver_licence']         = $driver_licence;
	$data['driver_licence_no']      = $driver_licence_no;
	$data['driver_sex']             = $driver_sex;
	$data['driver_joint_date']      = $driver_joint_date;
	$data['driver_note']            = $driver_note;
	$data['driver_rfid']            = $driver_rfid;
	$data['driver_licence_expired'] = $driver_licence_expired;
	$data['driver_siof']            = $driver_siof;
	$data['driver_siof_expired']    = $driver_siof_expired;
	$data['driver_group']           = $driver_group;
	$data['driver_idcard']          = strtoupper($driver_idcard);

	$this->dbtransporter->insert("driver", $data);
	$callback["error"] = false;
	$callback["message"] = "Add Driver Success";
	$callback["redirect"] = base_url()."driver";

	echo json_encode($callback);
	$this->dbtransporter->close();
	return;
	}

	function getVehicle(){
		$driver_id                = $this->input->post('id');
		$vehicle_user_id          = $this->sess->user_id;
		$datavehicle              = $this->driver_model->getalldatabyuserid("webtracking_vehicle", "vehicle_user_id", $vehicle_user_id);

		// GET DB TRANSPORTER
		$this->dbtransporter = $this->load->database("transporter", true);
		$this->dbtransporter->select("*");
		$this->dbtransporter->from("driver");
		$this->dbtransporter->where("driver_id", $driver_id);
		$q = $this->dbtransporter->get();
		if ($q->num_rows == 0) { return; }
		$row = $q->row();
		// echo "<pre>";
		// var_dump($row);die();
		// echo "<pre>";
		if ($row->driver_vehicle == 0) {
			$row2 = "Available / Vehicle Not Assigned";
		}else {
			// GET DB WEBTRACKING
			$this->db = $this->load->database("default", true);
			$this->db->select("*");
			$this->db->from("vehicle");
			$this->db->where("vehicle_id", $row->driver_vehicle);
			$q2 = $this->db->get();
				if ($q2->num_rows == 0) {
					$row2 = "Available / Vehicle Not Assigned";
				}else {
					 $q2->row();
					 $row2 = "Assigned To : " . $q2->row()->vehicle_no . " - " . $q2->row()->vehicle_name;
				}
		}

		$this->params['row']          = $row;
		$this->params['row2']         = $row2;
		$this->params['driver_id']    = $driver_id;
		$this->params["data_vehicle"] = $datavehicle;
		echo json_encode($this->params);
	}

	function assignnow(){
		$driver_id   = $this->input->post('driver_id');
		$driver_name = $this->input->post('driver_name');
		$user_id     = $this->input->post('user_id');
		$vehicle_id  = $this->input->post('vehicle_id');

		// echo "<pre>";
		// var_dump($driver_id.'-'.$driver_name.'-'.$user_id.'-'.$vehicle_id);die();
		// echo "<pre>";

		// GET DB TRANSPORTER
		$this->dbtransporter = $this->load->database("transporter", true);
		$this->dbtransporter->select("*");
		$this->dbtransporter->from("driver");
		$this->dbtransporter->where("driver_vehicle", $vehicle_id);
		$q = $this->dbtransporter->get();
		// if ($q->num_rows == 0) { return; }
		$row = $q->row();
			if ($q->num_rows > 0) {
				echo json_encode(array("msg" => "already"));
			}else {
			 	if ($vehicle_id == "makeavailable") {
				// FOR UPDATE TO TRANSPORTER_DRIVER
				$data = array(
					"driver_vehicle"      => "0"
				);

				$update = $this->driver_model->updateDatadbtransporter("driver", "driver_id", $driver_id, $data);
					if ($update) {
						// FOR INSERT TO LOG TABLE
						$getdatauser              = $this->driver_model->get1("webtracking_user", "user_id", $user_id);
						$getdatvehicle              = $this->driver_model->get1("webtracking_vehicle", "vehicle_id", $vehicle_id);
						$getdatadriver              = $this->driver_model->getalldatadbtransporter("driver", "driver_id", $driver_id);

						$data2 = array(
							"driver_history_vehicle_user_id" => "Set As Available",
							"driver_history_username"        => $getdatauser[0]['user_name'],
							"driver_history_vehicle_id"      => "Set As Available",
							"driver_history_vehicle_no"      => "Set As Available",
							"driver_history_vehicle_name"    => "Set As Available",
							"driver_history_driver_id"       => $getdatadriver[0]['driver_id'],
							"driver_history_driver_name"     => $getdatadriver[0]['driver_name'],
							"driver_history_creator"         => $this->sess->user_id
						);
						$insert = $this->driver_model->insertDataDbTransporter("driver_history", $data2);
							if ($insert) {
								echo json_encode(array("msg" => "success"));
							}else {
								echo json_encode(array("msg" => "error"));
							}
					}else {
						echo json_encode(array("msg" => "error"));
					}
			}else {
				// FOR UPDATE TO TRANSPORTER_DRIVER
				$data = array(
					"driver_vehicle"      => $vehicle_id
				);

				$update = $this->driver_model->updateDatadbtransporter("driver", "driver_id", $driver_id, $data);
					if ($update) {
						// FOR INSERT TO LOG TABLE
						$getdatauser   = $this->driver_model->get1("webtracking_user", "user_id", $user_id);
						$getdatvehicle = $this->driver_model->get1("webtracking_vehicle", "vehicle_id", $vehicle_id);
						$getdatadriver = $this->driver_model->getalldatadbtransporter("driver", "driver_id", $driver_id);

						$data2 = array(
							"driver_history_vehicle_user_id" => $getdatvehicle[0]['vehicle_user_id'],
							"driver_history_username"        => $getdatauser[0]['user_name'],
							"driver_history_vehicle_id"      => $getdatvehicle[0]['vehicle_id'],
							"driver_history_vehicle_no"      => $getdatvehicle[0]['vehicle_no'],
							"driver_history_vehicle_name"    => $getdatvehicle[0]['vehicle_name'],
							"driver_history_driver_id"       => $getdatadriver[0]['driver_id'],
							"driver_history_driver_name"     => $getdatadriver[0]['driver_name'],
							"driver_history_creator"         => $this->sess->user_id
						);
						$insert = $this->driver_model->insertDataDbTransporter("driver_history", $data2);
							if ($insert) {
								echo json_encode(array("msg" => "success"));
							}else {
								echo json_encode(array("msg" => "error"));
							}
					}else {
						echo json_encode(array("msg" => "error"));
					}
			}
		}
	}

	function edit() {
		if (! isset($this->sess->user_company)) {
			redirect(base_url());
		}
		$driver_id = $this->uri->segment(3);
		if ($driver_id) {
			$this->dbtransporter = $this->load->database("transporter", true);
			$this->dbtransporter->select("*");
			$this->dbtransporter->from("driver");
			$this->dbtransporter->where("driver_id", $driver_id);
			$q = $this->dbtransporter->get();
			if ($q->num_rows == 0) { return; }

			$row = $q->row();
			$this->params['row'] = $row;
			// echo "<pre>";
			// var_dump($row);die();
			// echo "<pre>";
			$this->params['title']          = "Edit Driver";
			$this->params['code_view_menu'] = "configuration";

			$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/driver/v_driver_edit', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}else{
			redirect(base_url()."driver");
		}
	}

	function profile() {
		if (! isset($this->sess->user_company)) {
			redirect(base_url());
		}
		
		$privilegecode = $this->sess->user_id_role;
		$driver_id     = $this->uri->segment(3);
		//$driver_id     = $iddriver_simper->driver_id;
		
		if ($driver_id) {
			$this->dbtransporter = $this->load->database("transporter", true);
			$this->dbtransporter->select("*");
			$this->dbtransporter->from("driver");
			$this->dbtransporter->where("driver_id", $driver_id);
			//$this->dbtransporter->join("driver_image", "driver_image_driver_id = driver_id", "left outer");
			$q = $this->dbtransporter->get();
			if ($q->num_rows == 0) { return; }

			$row = $q->row();
			$this->params['row'] = $row;
			

			$datalast_absensi                 = $this->getlast_absensi($driver_id);
			$total_absensi                    = count($datalast_absensi);
			
			$datavehicle_bycontractor = $this->getvehicle_bycompany($row->driver_company);
			$total_vehiclebycontractor       = count($datavehicle_bycontractor);
			
			$this->params['total_absensi']    = $total_absensi;
			$this->params['datalast_absensi'] = $datalast_absensi;
			
			$this->params['total_vehiclebycontractor']    = $total_vehiclebycontractor;
			$this->params['datavehicle_bycontractor'] = $datavehicle_bycontractor;
	
			
			$this->params['title']            = "Driver Profile";
			$this->params['code_view_menu']   = "configuration";

			$this->params["header"]           = $this->load->view('newdashboard/partial/headernew', $this->params, true);
			$this->params["chatsidebar"]      = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

			if ($privilegecode == 9) {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_driver', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/driver/v_driver_profile', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_new_driver", $this->params);
			}else {
				$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
				$this->params["content"]        = $this->load->view('newdashboard/driver/v_driver_profile', $this->params, true);
				$this->load->view("newdashboard/partial/template_dashboard_new_driver", $this->params);
			}
		}else{
			redirect(base_url()."driver");
		}
	}

	function clockin() {
		if (! isset($this->sess->user_company)) {
			redirect(base_url());
		}
		
		//$driver_id = 2887; //driver asdi
		$iddriver_simper = $this->getDriver_idsimper($this->sess->user_login);
		$driver_id = $iddriver_simper->driver_id;
		
		
		if($driver_id == 0 || $driver_id == ""){
			redirect(base_url()."driver");
		}

		if ($driver_id) {
			$this->dbtransporter = $this->load->database("transporter", true);
			$this->dbtransporter->select("*");
			$this->dbtransporter->from("driver");
			$this->dbtransporter->where("driver_id", $driver_id);
			//$this->dbtransporter->join("driver_image", "driver_image_driver_id = driver_id", "left outer");
			$q = $this->dbtransporter->get();
			if ($q->num_rows == 0) { return; }

			$row = $q->row();
			$this->params['row'] = $row;

			$datalast_absensi = $this->getlast_absensi($driver_id);
			$total_absensi = count($datalast_absensi);

			$this->params['total_absensi'] = $total_absensi;
			$this->params['datalast_absensi'] = $datalast_absensi;
			$this->params['title']          = "Jam Masuk Driver";
			$this->params['code_view_menu'] = "configuration";

			$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_driver', $this->params, true);
			$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/driver/v_driver_clockin', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new_driver", $this->params);
		}else{
			redirect(base_url()."driver");
		}
	}

	function clockin_save() {
		if (! isset($this->sess->user_company)) {
			redirect(base_url());
		}

		$this->dbts 		  = $this->load->database("webtracking_ts", true);
		$driver_id      	  = isset($_POST['driver_id']) ? $_POST['driver_id']: "";
		$driver_name          = isset($_POST['driver_name']) ? $_POST['driver_name']:  "";
		$driver_idcard        = isset($_POST['driver_idcard']) ? $_POST['driver_idcard']: "";
		$driver_shift         = isset($_POST['driver_shift']) ? $_POST['driver_shift']: "";
		$driver_photo_text    = isset($_POST['driver_photo_text']) ? $_POST['driver_photo_text']: "";
		$driver_coord         = isset($_POST['driver_coord']) ? $_POST['driver_coord']: "";

		$nowtime = date("Y-m-d H:i:s");
		$nowtime_wita = date("Y-m-d H:i:s", strtotime($nowtime . "+1hours"));


		$error = "";

		if ($driver_id == "")
		{
			$error .= "- ID Driver tidak diketahui! \n";
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}

		if ($driver_name == "")
		{
			$error .= "- Nama Driver tidak diketahui! \n";
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}

		if ($driver_idcard == "")
		{
			$error .= "- ID Card/Simper tidak diketahui! \n";
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}

		if ($driver_shift == "" )
		{
			$error .= "- Silahkan Pilih Shift! \n";
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}else{
			$data_shift = explode(" ",$driver_shift);

		}

		if ($driver_coord == "" )
		{
			$error .= "- Posisi tidak diketahui, silahkan login ulang atau gunakan browser lain! \n";
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}

		if ($driver_photo_text == "")
		{
			$error .= "- Foto Selfie tidak ada! Mohon Foto Selfie dengan latar no lambung yang digunakan \n";
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}

		/* if ($error != "")
		{
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		} */

		unset($data);
		$data['absensi_driver_id']         = $driver_id;
		$data['absensi_driver_name']       = $driver_name;
		$data['absensi_driver_idcard']     = $driver_idcard;
		$data['absensi_driver_time']       = $nowtime_wita;
		$data['absensi_shift_type']        = $data_shift[0];
		$data['absensi_shift_time']        = $data_shift[1]."-".$data_shift[3];
		$data['absensi_clock_in']      	   = $nowtime_wita;
		$data['absensi_clock_in_status']   = 1; //status ada data time
		$data['absensi_clock_in_coord']    = $driver_coord;
		$data['absensi_photo_txt']         = $driver_photo_text;
		$data['absensi_status']            = 1; //sudah absen

		$this->dbts->insert("ts_driver_absensi", $data);
		$callback["error"] = false;
		$callback["message"] = "Berhasil Absen Jam Masuk";
		$callback["redirect"] = base_url()."driver";

		echo json_encode($callback);
		$this->dbts->close();
		return;
	}

	function clockout() {
		if (! isset($this->sess->user_company)) {
			redirect(base_url());
		}
		
		//$driver_id = 2887; //driver asdi
		$iddriver_simper = $this->getDriver_idsimper($this->sess->user_login);
		$driver_id = $iddriver_simper->driver_id;
		
		if($driver_id == 0 || $driver_id == ""){
			redirect(base_url()."driver");
		}

		if ($driver_id) {
			$datalast_absensi = $this->getlast_absensi($driver_id);
			$total_absensi = count($datalast_absensi);

			$this->params['total_absensi']  = $total_absensi;
			$this->params['row'] 			= $datalast_absensi;
			$this->params['title']          = "Jam Keluar Driver";
			$this->params['code_view_menu'] = "configuration";

			$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_driver', $this->params, true);
			$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/driver/v_driver_clockout', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new_driver", $this->params);
		}else{
			redirect(base_url()."driver");
		}
	}

	function clockout_save() {
		if (! isset($this->sess->user_company)) {
			redirect(base_url());
		}

		$this->dbts 		  = $this->load->database("webtracking_ts", true);
		$absensi_clock_in     = isset($_POST['absensi_clock_in']) ? $_POST['absensi_clock_in']: "";
		$absensi_id      	  = isset($_POST['absensi_id']) ? $_POST['absensi_id']: "";
		$driver_coord         = isset($_POST['driver_coord']) ? $_POST['driver_coord']: "";
		$absensi_vehicle_id   = isset($_POST['absensi_vehicle_id']) ? $_POST['absensi_vehicle_id']: "";
		
		
		$nowtime = date("Y-m-d H:i:s");
		$nowtime_wita = date("Y-m-d H:i:s", strtotime($nowtime . "+1hours"));

		$error = "";

		if ($absensi_id == "")
		{
			$error .= "- ID Absensi tidak diketahui! \n";
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}
		
		if ($absensi_vehicle_id == "")
		{
			$error .= "- ID Unit tidak diketahui! \n";
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}

		if ($driver_coord == "" )
		{
			$error .= "- Posisi tidak diketahui, silahkan login ulang atau gunakan browser lain! \n";
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}


		$duration = get_time_difference($absensi_clock_in, $nowtime_wita);

									$start_1 = dbmaketime($absensi_clock_in);
									$end_1 = dbmaketime($nowtime_wita);
									$duration_sec = $end_1 - $start_1;

                                    $show = "";
                                    if($duration[0]!=0)
                                    {
                                        $show .= $duration[0] ." Day ";
                                    }
                                    if($duration[1]!=0)
                                    {
                                        $show .= $duration[1] ." Hour ";
                                    }
                                    if($duration[2]!=0)
                                    {
                                        $show .= $duration[2] ." Min ";
                                    }
                                    if($show == "")
                                    {
                                        $show .= "0 Min";
                                    }

		unset($data);

		$data['absensi_clock_out']         = $nowtime_wita;
		$data['absensi_clock_out_status']  = 1; //status ada data time
		$data['absensi_clock_out_coord']   = $driver_coord;
		$data['absensi_duration']          = $show;
		$data['absensi_duration_sec']      = $duration_sec;
		$data['absensi_status']            = 2; //sudah absen keluar

		$this->dbts->where('absensi_id', $absensi_id);
		$this->dbts->update('ts_driver_absensi', $data);
		
		//update vehicle tms (available)
		unset($datav);
		$datav['vehicle_tms'] = "0000";
				
		$this->db->where('vehicle_id', $absensi_vehicle_id);
		$this->db->limit(1);
		$this->db->update('vehicle', $datav);

		$callback["error"] = false;
		$callback["message"] = "Berhasil Absen Jam Keluar";
		$callback["redirect"] = base_url()."driver";

		echo json_encode($callback);
		$this->dbts->close();
		return;
	}

	function breakdownstart() {
		if (! isset($this->sess->user_company)) {
			redirect(base_url());
		}
		//$driver_id = $this->sess->user_app;
		//$driver_id = 2887; //driver asdi
		$iddriver_simper = $this->getDriver_idsimper($this->sess->user_login);
		$driver_id = $iddriver_simper->driver_id;
		
		if($driver_id == 0 || $driver_id == ""){
			redirect(base_url()."driver");
		}

		if ($driver_id) {
			$datadriver = $this->get_driverinfo($driver_id);

			$this->params['row'] 			= $datadriver;
			$this->params['title']          = "Breakdown Driver";
			$this->params['code_view_menu'] = "configuration";

			$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_driver', $this->params, true);
			$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/driver/v_driver_breakdownstart', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new_driver", $this->params);
		}else{
			redirect(base_url()."driver");
		}
	}

	function breakdownstart_save() {
		if (! isset($this->sess->user_company)) {
			redirect(base_url());
		}

		$this->dbts 		  = $this->load->database("webtracking_ts", true);
		$breakdown_start_time     = isset($_POST['breakdown_start_time']) ? $_POST['breakdown_start_time']: "";
		$breakdown_driver_id      		  = isset($_POST['breakdown_driver_id']) ? $_POST['breakdown_driver_id']: "";
		$breakdown_driver_name      		  = isset($_POST['breakdown_driver_name']) ? $_POST['breakdown_driver_name']: "";
		$breakdown_driver_idcard      		  = isset($_POST['breakdown_driver_idcard']) ? $_POST['breakdown_driver_idcard']: "";
		$breakdown_start_coord    = isset($_POST['breakdown_start_coord']) ? $_POST['breakdown_start_coord']: "";
		$breakdown_vehicle_no      		  = isset($_POST['breakdown_vehicle_no']) ? $_POST['breakdown_vehicle_no']: "";
		$breakdown_vehicle_device      		  = isset($_POST['breakdown_vehicle_device']) ? $_POST['breakdown_vehicle_device']: "";
		$breakdown_vehicle_mv03      		  = isset($_POST['breakdown_vehicle_mv03']) ? $_POST['breakdown_vehicle_mv03']: "";

		$nowtime = date("Y-m-d H:i:s");
		$nowtime_wita = date("Y-m-d H:i:s", strtotime($nowtime . "+1hours"));

		$error = "";

		if ($breakdown_driver_id == "")
		{
			$error .= "- ID Breakdown tidak diketahui! \n";
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}

		if ($breakdown_start_coord == "" )
		{
			$error .= "- Posisi tidak diketahui, silahkan login ulang atau gunakan browser lain! \n";
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}

		unset($data);

		$data['breakdown_start_time']      = $nowtime_wita;
		$data['breakdown_start_status']    = 1; //status ada data time
		$data['breakdown_start_coord']     = $breakdown_start_coord;
		$data['breakdown_status']          = 2;
		$data['breakdown_driver_id']      = $breakdown_driver_id;
		$data['breakdown_driver_name']    = $breakdown_driver_name;
		$data['breakdown_driver_idcard']  = $breakdown_driver_idcard;
		$data['breakdown_vehicle_no']     = $breakdown_vehicle_no;
		$data['breakdown_vehicle_mv03']   = $breakdown_vehicle_mv03;
		$data['breakdown_vehicle_device']  = $breakdown_vehicle_device;

		$this->dbts->insert("ts_driver_breakdown", $data);

		//send to telegram


		$callback["error"] = false;
		$callback["message"] = "Breakdown Mulai";
		$callback["redirect"] = base_url()."driver";

		echo json_encode($callback);
		$this->dbts->close();
		return;
	}

	function breakdownfinish() {
		if (! isset($this->sess->user_company)) {
			redirect(base_url());
		}
		
		//$driver_id = 2887; //driver asdi
		
		$iddriver_simper = $this->getDriver_idsimper($this->sess->user_login);
		$driver_id = $iddriver_simper->driver_id;
		
		if($driver_id == 0 || $driver_id == ""){
			redirect(base_url()."driver");
		}

		if ($driver_id) {
			$datadriver = $this->get_driverinfo($driver_id);

			$this->params['row'] 			= $datadriver;
			$this->params['title']          = "Breakdown Selesai";
			$this->params['code_view_menu'] = "configuration";

			$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_driver', $this->params, true);
			$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/driver/v_driver_breakdownfinish', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new_driver", $this->params);
		}else{
			redirect(base_url()."driver");
		}
	}

	function breakdownfinish_save() {
		if (! isset($this->sess->user_company)) {
			redirect(base_url());
		}

		$this->dbts 		  = $this->load->database("webtracking_ts", true);
		$breakdown_finish_time     = isset($_POST['breakdown_finish_time']) ? $_POST['breakdown_finish_time']: "";
		$breakdown_driver_id      	  = isset($_POST['breakdown_driver_id']) ? $_POST['breakdown_driver_id']: "";
		$breakdown_finish_coord         = isset($_POST['breakdown_finish_coord']) ? $_POST['breakdown_finish_coord']: "";

		$nowtime = date("Y-m-d H:i:s");
		$nowtime_wita = date("Y-m-d H:i:s", strtotime($nowtime . "+1hours"));

		$error = "";

		if ($breakdown_id == "")
		{
			$error .= "- ID Breakdown tidak diketahui! \n";
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}

		if ($breakdown_start_coord == "" )
		{
			$error .= "- Posisi tidak diketahui, silahkan login ulang atau gunakan browser lain! \n";
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}


		$duration = get_time_difference($breakdown_start_time, $nowtime_wita);

									$start_1 = dbmaketime($breakdown_start_time);
									$end_1 = dbmaketime($nowtime_wita);
									$duration_sec = $end_1 - $start_1;

                                    $show = "";
                                    if($duration[0]!=0)
                                    {
                                        $show .= $duration[0] ." Day ";
                                    }
                                    if($duration[1]!=0)
                                    {
                                        $show .= $duration[1] ." Hour ";
                                    }
                                    if($duration[2]!=0)
                                    {
                                        $show .= $duration[2] ." Min ";
                                    }
                                    if($show == "")
                                    {
                                        $show .= "0 Min";
                                    }

		unset($data);

		$data['breakdown_finish_time']       = $nowtime_wita;
		$data['breakdown_finish_status'] 	 = 1; //status ada data time
		$data['breakdown_finish_coord']  	 = $driver_coord;
		$data['breakdown_duration']          = $show;
		$data['breakdown_duration_sec']      = $duration_sec;
		$data['breakdown_status']            = 2; //sudah absen keluar

		$this->dbts->where('breakdown_id', $breakdown_id);
		$this->dbts->update('ts_breakdown_driver', $data);

		$callback["error"] = false;
		$callback["message"] = "Breakdown Selesai";
		$callback["redirect"] = base_url()."driver";

		echo json_encode($callback);
		$this->dbts->close();
		return;
	}

	function update() {

		if (! isset($this->sess->user_company)) {
			redirect(base_url());
		}

		$this->dbtransporter = $this->load->database('transporter', true);

		$driver_company         = $this->sess->user_company;
		$driver_id              = $this->input->post('driver_id');


		$driver_name            = $this->input->post('driver_name');
		$driver_address         = $this->input->post('driver_address');
		$driver_phone           = $this->input->post('driver_phone');
		$driver_mobile          = $this->input->post('driver_mobile');
		$driver_mobile2         = $this->input->post('driver_mobile2');
		$driver_licence         = $this->input->post('driver_licence');
		$driver_licence_no      = $this->input->post('driver_licence_no');
		$driver_sex             = $this->input->post('driver_sex');
		$driver_joint_date      = $this->input->post('driver_joint_date');
		$driver_note            = $this->input->post('driver_note');
		$driver_rfid            = $this->input->post('driver_rfid');

		$driver_licence_expired = $this->input->post('driver_licence_expired');
		$driver_siof            = $this->input->post('driver_siof');
		$driver_siof_expired    = $this->input->post('driver_siof_expired');
		$driver_group           = $this->input->post('driver_group');
		$driver_idcard          = $this->input->post('driver_idcard');

		$data = array(
						'driver_company'          => $driver_company,
					  'driver_id'              => $driver_id,
					  'driver_name'            => $driver_name,
					  'driver_idcard'          => strtoupper($driver_idcard),
					  'driver_address'         => $driver_address,
					  'driver_phone'           => $driver_phone,
					  'driver_mobile'          => $driver_mobile,
					  'driver_mobile2'         => $driver_mobile2,
					  'driver_licence'         => $driver_licence,
					  'driver_licence_no'      => $driver_licence_no,
					  'driver_sex'             => $driver_sex,
					  'driver_joint_date'      => date("Y-m-d", strtotime($driver_joint_date)),
					  'driver_licence_expired' => date("Y-m-d", strtotime($driver_licence_expired)),
					  'driver_siof'            => $driver_siof,
					  'driver_siof_expired'    => date("Y-m-d", strtotime($driver_siof_expired)),
					  'driver_note'            => $driver_note,
						'driver_rfid'            => $driver_rfid,
					  'driver_group'           => $driver_group
					);
		// echo "<pre>";
		// var_dump($data);die();
		// echo "<pre>";

		$this->dbtransporter->where('driver_id', $driver_id);
		$this->dbtransporter->update('driver', $data);
		// $this->dbtransporter->close();

		$callback["error"] = false;
		$callback["message"] = "Update Driver Success";
		$callback["redirect"] = base_url()."driver";

		echo json_encode($callback);
		$this->dbtransporter->close();
		return;
	}

	function upload_image() {
		if (! isset($this->sess->user_company)) {
			redirect(base_url());
		}

		$driver_id = $this->input->post("id");
		$this->load->helper(array('form'));

		$this->dbtransporter = $this->load->database("transporter", true);
		$this->dbtransporter->select("*");
		$this->dbtransporter->from("driver");
		$this->dbtransporter->where("driver_id", $driver_id);
		$q   = $this->dbtransporter->get();
		$row = $q->row();

		//select driver image
		$this->dbtransporter->select("*");
		$this->dbtransporter->from("driver_image");
		$this->dbtransporter->where("driver_image_driver_id", $driver_id);
		$q         = $this->dbtransporter->get();
		$row_image = $q->row();

		$this->dbtransporter->close();

		$params['row_image']    = $row_image;
		$params['row']          = $row;
		$params["title"]        = "Manage Driver - Upload Images";
		$params["driver_id"]    = $driver_id;
		$params["error_upload"] = "";
		echo json_encode($params);
		//$this->load->view("templatesess", $this->params);
	}

	function save_image() {
		$config['upload_path']   = './assets/transporter/images/photo/';
		$config['allowed_types'] = 'gif|jpeg|jpg|png';
		$config['max_size']      = '200';
		$config['max_width']     = '1024';
		$config['max_height']    = '1024';

		$this->load->library('upload', $config);
		$driver_image_driver_id = $this->input->post("driver_id");
		// echo "<pre>";
		// var_dump($driver_image_driver_id);die();
		// echo "<pre>";

		if (!$this->upload->do_upload()) {
			$error = array('error' => $this->upload->display_errors());
			echo $error['error']. '<br>'. 'Please press back button and try another image.';
			// print_r($error);exit();
			//$this->load->view('transporter/driver/upload_image', $error);
			// $this->load->view('transporter/driver/upload_error', $error);
			//redirect(base_url()."transporter/driver");
		}else {
			$this->dbtransporter = $this->load->database("transporter", true);
			$data     = array('upload_data' => $this->upload->data());

			// echo "<pre>";
			// var_dump($data);die();
			// echo "<pre>";

			$driver_image_file_name      = $data['upload_data']['file_name'];
			$driver_image_file_type      = $data['upload_data']['file_type'];
			$driver_image_file_path      = $data['upload_data']['file_path'];
			$driver_image_full_path      = $data['upload_data']['full_path'];
			$driver_image_raw_name       = $data['upload_data']['raw_name'];
			$driver_image_orig_name      = $data['upload_data']['orig_name'];
			$driver_image_client_name    = $data['upload_data']['client_name'];
			$driver_image_file_ext       = $data['upload_data']['file_ext'];
			$driver_image_file_size      = $data['upload_data']['file_size'];
			$driver_image_is_image       = $data['upload_data']['is_image'];
			$driver_image_image_width    = $data['upload_data']['image_width'];
			$driver_image_image_height   = $data['upload_data']['image_height'];
			$driver_image_image_type     = $data['upload_data']['image_type'];
			$driver_image_image_size_str = $data['upload_data']['image_size_str'];

			unset($data_insert);
				$data_insert['driver_image_driver_id']      = $driver_image_driver_id;
				$data_insert['driver_image_file_name']      = $driver_image_file_name;
				$data_insert['driver_image_file_path']      = $driver_image_file_path;
				$data_insert['driver_image_full_path']      = $driver_image_full_path;
				$data_insert['driver_image_raw_name']       = $driver_image_raw_name;
				$data_insert['driver_image_orig_name']      = $driver_image_orig_name;
				$data_insert['driver_image_client_name']    = $driver_image_client_name;
				$data_insert['driver_image_file_ext']       = $driver_image_file_ext;
				$data_insert['driver_image_file_size']      = $driver_image_file_size;
				$data_insert['driver_image_is_image']       = $driver_image_is_image;
				$data_insert['driver_image_image_width']    = $driver_image_image_width;
				$data_insert['driver_image_image_height']   = $driver_image_image_height;
				$data_insert['driver_image_image_type']     = $driver_image_image_type;
				$data_insert['driver_image_image_size_str'] = $driver_image_image_size_str;

			//cari apakah ada di table transporter_driver_image
			$this->dbtransporter->select("*");
			$this->dbtransporter->from("driver_image");
			$this->dbtransporter->where("driver_image_driver_id", $driver_image_driver_id);
			$this->dbtransporter->limit(1);
			$q = $this->dbtransporter->get();



			//Jika 0 maka Insert
			if ($q->num_rows == 0) {
				$this->dbtransporter->insert("driver_image", $data_insert);
			}
			else {
				//Jika ada maka update
        $this->dbtransporter->where("driver_image_driver_id", $driver_image_driver_id);
				$this->dbtransporter->update("driver_image", $data_insert);
			}

			redirect(base_url()."driver");
			//$this->load->view('transporter/driver/upload_success', $data);
		}
	}

	function getlast_absensi($id)
	{
		$this->dbts = $this->load->database('webtracking_ts', true);
		$this->dbts->order_by("absensi_id", "desc");
		$this->dbts->where("absensi_driver_id", $id);
		$this->dbts->where("absensi_clock_in_status", 1);//sudah absen
		//$this->dbts->where("absensi_status", 1);
		$this->dbts->where("absensi_flag", 0);
		$qabs = $this->dbts->get("ts_driver_absensi");
		$row_qabs = $qabs->row();
		return $row_qabs;
	}

	function getlast_breakdown($id)
	{
		$this->dbts = $this->load->database('webtracking_ts', true);
		$this->dbts->order_by("breakdown_id", "desc");
		$this->dbts->where("breakdown_driver_id", $id);
		$this->dbts->where("breakdown_start_status", 1);
		//$this->dbts->where("breakdown_status", 1);
		$this->dbts->where("breakdown_flag", 0);
		$qabs = $this->dbts->get("ts_driver_breakdown");
		$row_qabs = $qabs->row();
		return $row_qabs;
	}

	function getsyncunit_byidcard()
	{
		$this->dbts 		  = $this->load->database("webtracking_ts", true);
		$absensi_id      	 = isset($_POST['absensi_id']) ? $_POST['absensi_id']: "";
		$absensi_idcard       = isset($_POST['absensi_idcard']) ? $_POST['absensi_idcard']: "";
		$absensi_clock_in     = isset($_POST['absensi_clock_in']) ? $_POST['absensi_clock_in']:  "";
		
		//idcard = idsimper
		//$absensi_clock_in_str = strtotime($absensi_clock_in);
		$absensi_clock_in_wib = date("Y-m-d H:i:s", strtotime($absensi_clock_in . "-0hours"));

		if ($absensi_id == "")
		{
			$error = "Terjadi Kesalahan! Data Absensi tidak valid \n";
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}

		if ($absensi_idcard == "")
		{
			$error = "Terjadi Kesalahan! Data ID SIMPER tidak valid \n";
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}

		if ($absensi_clock_in == "")
		{
			$error = "Terjadi Kesalahan! Data Jam Absensi tidak valid \n";
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}

		$face_detect = "";
		$vehicle_no = "";
		$filter_date = date("Y-m-d", strtotime($absensi_clock_in));
		$filter_date_time = date("Y-m-d H:i:s", strtotime($filter_date." "."00:00:00"));

		$this->dbts->order_by("change_driver_time", "desc");
		$this->dbts->where("change_driver_id", $absensi_idcard);
		$this->dbts->where("change_driver_time >=", $filter_date_time);//ambil dari date yg sama dengan jam absen
		//$this->dbts->where("absensi_status", 1);
		$this->dbts->where("change_driver_flag", 0);
		$this->dbts->limit(1);
		$qabs = $this->dbts->get("ts_driver_change_new");
		$row_qabs = $qabs->row();
		$total_row = count($row_qabs);
		if ($total_row == 0)
		{
			$error = "Tidak dapat terhubung! Mohon Verifikasi Wajah kembali \n";
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}else{

			$face_detect = $row_qabs->change_driver_time;
			$face_detect_wita = date("Y-m-d H:i:s", strtotime($face_detect . "+0hours"));
			$dt_vehicle = $this->get_vehicle_bymv03($row_qabs->change_imei);
			$total_dt_vehicle = count($dt_vehicle);

			if($total_dt_vehicle == 0){
				$error = "Unit Kendaraan belum terdaftar. Silahkan hubungi Admin. \n";
				$callback['error'] = true;
				$callback['message'] = $error;

				echo json_encode($callback);
				return;
			}else{

				$vehicle_no = $dt_vehicle->vehicle_no;
				$vehicle_id = $dt_vehicle->vehicle_id;

				unset($data);
				
				$data['absensi_vehicle_id'] = $dt_vehicle->vehicle_id;
				$data['absensi_vehicle_device'] = $dt_vehicle->vehicle_device;
				$data['absensi_vehicle_no']     = $vehicle_no;
				$data['absensi_vehicle_mv03']   = $dt_vehicle->vehicle_mv03;
				$data['absensi_face_detected']   = $face_detect_wita;

				$this->dbts->where('absensi_id', $absensi_id);
				$this->dbts->update('ts_driver_absensi', $data);
				
				//update vehicle tms (used)
				unset($datav);
				$datav['vehicle_tms'] = $absensi_idcard;
				
				$this->db->where('vehicle_id', $vehicle_id);
				$this->db->limit(1);
				$this->db->update('vehicle', $datav);
				
			}

		}



		$callback["error"] = false;
		$callback["message"] = "Berhasil Terhubung";
		$callback["vehicle_no"] = $vehicle_no;
		$callback["face_detect"] = $face_detect;

		echo json_encode($callback);
		$this->dbts->close();
		return;

	}
	
	function getsyncunit_byidcard_manual()
	{
		$this->dbts 		  = $this->load->database("webtracking_ts", true);
		$absensi_id      	 = isset($_POST['absensi_id']) ? $_POST['absensi_id']: "";
		$absensi_idcard       = isset($_POST['absensi_idcard']) ? $_POST['absensi_idcard']: "";
		$absensi_clock_in     = isset($_POST['absensi_clock_in']) ? $_POST['absensi_clock_in']:  "";
		$absensi_vehicle_manual     = isset($_POST['absensi_vehicle_manual']) ? $_POST['absensi_vehicle_manual']:  0;
		
		//idcard = idsimper
		//$absensi_clock_in_str = strtotime($absensi_clock_in);
		$absensi_clock_in_wib = date("Y-m-d H:i:s", strtotime($absensi_clock_in . "-0hours"));

		if ($absensi_id == "")
		{
			$error = "Terjadi Kesalahan! Data Absensi tidak valid \n";
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}

		if ($absensi_idcard == "")
		{
			$error = "Terjadi Kesalahan! Data ID SIMPER tidak valid \n";
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}

		if ($absensi_clock_in == "")
		{
			$error = "Terjadi Kesalahan! Data Jam Absensi tidak valid \n";
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}
		
		if ($absensi_vehicle_manual == 0)
		{
			$error = "Terjadi Kesalahan! Data kendaraan yg dipilih tidak valid \n";
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}

		$face_detect = "";
		$vehicle_no = "";

		

			$face_detect = date("Y-m-d H:i:s");
			$face_detect_wita = date("Y-m-d H:i:s", strtotime($face_detect . "+1hours"));
			
			$dt_vehicle = $this->get_vehicle_byid($absensi_vehicle_manual);
			$total_dt_vehicle = count($dt_vehicle);

			if($total_dt_vehicle == 0){
				$error = "Unit Kendaraan belum terdaftar. Silahkan hubungi Admin. \n";
				$callback['error'] = true;
				$callback['message'] = $error;

				echo json_encode($callback);
				return;
			}else{

				$vehicle_no = $dt_vehicle->vehicle_no;
				$vehicle_id = $dt_vehicle->vehicle_id;

				unset($data);
				
				$data['absensi_vehicle_id'] = $dt_vehicle->vehicle_id;
				$data['absensi_vehicle_device'] = $dt_vehicle->vehicle_device;
				$data['absensi_vehicle_no']     = $vehicle_no;
				$data['absensi_vehicle_mv03']   = $dt_vehicle->vehicle_mv03;
				$data['absensi_face_detected']   = $face_detect_wita;
				$data['absensi_vehicle_manual']   = 1;
				$this->dbts 		  = $this->load->database("webtracking_ts", true);
				$this->dbts->where('absensi_id', $absensi_id);
				$this->dbts->update('ts_driver_absensi', $data);
				
				//update vehicle tms (used)
				unset($datav);
				$datav['vehicle_tms'] = $absensi_idcard;
				
				$this->db->where('vehicle_id', $vehicle_id);
				$this->db->limit(1);
				$this->db->update('vehicle', $datav);
				
			}
			

		$callback["error"] = false;
		$callback["message"] = "Berhasil Terhubung";
		$callback["vehicle_no"] = $vehicle_no;
		$callback["face_detect"] = $face_detect_wita;

		echo json_encode($callback);
		$this->dbts->close();
		return;

	}

	function sosdigital_save() {

		$this->dbts 			 = $this->load->database("webtracking_ts", true);
		$absensi_id2      		 = isset($_POST['absensi_id2']) ? $_POST['absensi_id2']: "";
		$absensi_driver_name     = isset($_POST['absensi_driver_name']) ? $_POST['absensi_driver_name']: "";
		$absensi_driverid      		 = isset($_POST['absensi_driverid']) ? $_POST['absensi_driverid']: "";
		$absensi_vehicle_no      = isset($_POST['absensi_vehicle_no']) ? $_POST['absensi_vehicle_no']: "";
		$absensi_idcard          = isset($_POST['absensi_idcard']) ? $_POST['absensi_idcard']: "";
		$now_coord        		 = isset($_POST['now_coord']) ? $_POST['now_coord']: "";

		$ex_coord = explode(",",$now_coord);
		$position =  $this->getPosition_other($ex_coord[1], $ex_coord[0]);
		$position_name = "";
		if(isset($position)){
			$ex_position = explode(",",$position->display_name);
			if(count($ex_position)>0){
				$position_name = $ex_position[0];
				}else{
					$position_name = $ex_position[0];
				}
			}else{
				$position_name = $position->display_name;
			}


		$nowtime = date("Y-m-d H:i:s");
		$nowtime_wita = date("Y-m-d H:i:s", strtotime($nowtime . "+1hours"));

		$error = "";

		if (! isset($this->sess->user_company)) {
			$error = "Sesi Akun Anda Habis! Silahkan Login Ulang \n";
			$callback['error'] = true;
			$callback['message'] = $error;
			$callback["redirect"] = base_url()."driver";
			echo json_encode($callback);
			return;
		}

		unset($data);

		$data['sos_driver_id']           = $absensi_driverid;
		$data['sos_driver_name']  		 = $absensi_driver_name;
		$data['sos_driver_idcard']       = $absensi_idcard;
		$data['sos_driver_time']     	 = $nowtime_wita;
		$data['sos_vehicle_no']          = $absensi_vehicle_no;
		$data['sos_coord']           	 = $now_coord;
		$data['sos_position']            = $position_name;
		$data['sos_absensi_id']          = $absensi_id2;
		$this->dbts->insert('ts_driver_sos', $data);

		$callback["error"] = false;
		$callback["message"] = "Anda telah menekan tombol SOS";
		$callback["redirect"] = base_url()."driver";

		echo json_encode($callback);
		$this->dbts->close();
		return;
	}

	function getPosition_other($longitude, $latitude)
	{
		//$api = $this->config->item('GOOGLE_MAP_API_KEY');
		$api = "AIzaSyCGr6BW7vPItrWq95DxMvL292Kf6jHNA5c"; //lacaktranslog prem
		//$georeverse = $this->gpsmodel->GeoReverse($latitude, $longitude);
		$georeverse = $this->gpsmodel->getLocation_byGeoCode($latitude, $longitude, $api);

		return $georeverse;
	}

	function delete_driver($id)
	{
		$this->dbtransporter = $this->load->database("transporter",true);
		$data["driver_status"] = 2;
		$this->dbtransporter->where("driver_id", $id);
		if($this->dbtransporter->update("driver", $data)){
			$callback['message'] = "Data has been deleted, PLEASE REFRESH PAGE";
			$callback['error'] = false;
		}else{
			$callback['message'] = "Failed delete data";
			$callback['error'] = true;
		}
		echo json_encode($callback);
	}

	function get_driverinfo($driver_id){
			$this->dbtransporter = $this->load->database("transporter", true);
			$this->dbtransporter->select("*");
			$this->dbtransporter->from("driver");
			$this->dbtransporter->where("driver_id", $driver_id);
			$this->dbtransporter->join("driver_image", "driver_image_driver_id = driver_id", "left outer");
			$q = $this->dbtransporter->get();
			$row = $q->row();
			return $row;

	}
	
	function getDriver_idsimper($idlogin){
		
		$this->dbtrans = $this->load->database("transporter",true);
		$this->dbtrans->select("driver_id");
		$this->dbtrans->order_by("driver_id", "desc");
        $this->dbtrans->where("driver_idcard", $idlogin);
		$this->dbtrans->where("driver_status", 1);
		$this->dbtrans->limit(1);
        $qdrv = $this->dbtrans->get('driver');
        $rowdrv = $qdrv->row();
		
		$this->dbtrans->close();
		$this->dbtrans->cache_delete_all();
		
		return $rowdrv;
	}
	
	function getvehicle_bycompany($idcom)
	{
		$this->dbts = $this->load->database('default', true);
		$this->dbts->select("vehicle_no,vehicle_id,vehicle_device");
		$this->dbts->order_by("vehicle_no", "asc");
		$this->dbts->where("vehicle_company", $idcom);
		$this->dbts->where("vehicle_status", 1);
		$this->dbts->where("vehicle_tms", "0000");
		$qunit = $this->dbts->get("vehicle");
		$row_unit = $qunit->result_array();
		
		$this->dbts->close();
		$this->dbts->cache_delete_all();
		
		return $row_unit;
	}
	
	function get_vehicle_byid($idvehicle)
	{
		$this->dbts = $this->load->database('default', true);
		$this->dbts->select("vehicle_no,vehicle_id,vehicle_device,vehicle_mv03");
		$this->dbts->order_by("vehicle_no", "asc");
		$this->dbts->where("vehicle_id", $idvehicle);
		$this->dbts->where("vehicle_status", 1);
		$qunit = $this->dbts->get("vehicle");
		$row_unit = $qunit->row();
		
		$this->dbts->close();
		$this->dbts->cache_delete_all();
		
		return $row_unit;
	}

}
