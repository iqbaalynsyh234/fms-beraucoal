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

		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

	}

	function index()
	{
		ini_set('display_errors', 1);

		//print_r("DISINI");exit();
		$this->dbtransporter = $this->load->database('transporter', true);
		$driver_company      = $this->sess->user_company;
		$driver_group        = $this->sess->user_group;
		$vehicle_user_id     = $this->sess->user_id;
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

		$this->params['code_view_menu'] = "configuration";
		$this->params['title']          = "Driver";

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
		$this->params["content"]        = $this->load->view('newdashboard/driver/v_driver', $this->params, true);
		$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
	}

	function get_vehicle()
	{
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_company", $this->sess->user_company);
		$qvehicle = $this->db->get("vehicle");
		$row_vehicle = $qvehicle->result();
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

}
