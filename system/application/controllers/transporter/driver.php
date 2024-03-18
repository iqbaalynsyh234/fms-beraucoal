<?php
include "base.php";

class Driver extends Base {

	function Driver()
	{
		parent::Base();
		if (! isset($this->sess->user_company)){
		redirect(base_url());
		}
		$this->load->model("driver_model");
		$this->load->model("gpsmodel");

	}

	function index($field ='all', $keyword='all', $offset=0)
	{
		ini_set('display_errors', 1);

		//print_r("DISINI");exit();
		$this->dbtransporter = $this->load->database('transporter', true);
		$driver_company = $this->sess->user_company;
		$driver_group = $this->sess->user_group;

		if (!$driver_company){
		redirect(base_url());
		}
		//ssi company
		if($this->sess->user_company == 356)
		{
			$row_vehicle = $this->get_vehicle();
		}
		switch($field){
		case "driver_name":
			$this->dbtransporter->where("driver_name LIKE '%".$keyword."%'", null);
		break;
		case "driver_idcard":
			$this->dbtransporter->where("driver_idcard LIKE '%".$keyword."%'", null);
		break;
		}
		if($this->sess->user_group == 0){
			$this->dbtransporter->where("driver_company", $driver_company);
		}else
		{
			$this->dbtransporter->where("driver_group", $driver_group);
		}
		$this->dbtransporter->where("driver_status", 1);
		$this->dbtransporter->orderby("driver_name","asc");
		$q = $this->dbtransporter->get("driver", 10, $offset);
		$rows = $q->result();

		switch($field){
		case "driver_name":
			$this->dbtransporter->where("driver_name LIKE '%".$keyword."%'", null);
		break;
		case "driver_idcard":
			$this->dbtransporter->where("driver_idcard LIKE '%".$keyword."%'", null);
		break;
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
		$rowstotal = $qtotal->result();

		// GET ASSIGNED VEHICLE STATUS
		$row2 = $this->driver_model->index1();

		$total = count($rowstotal);

		$config['uri_segment'] = 6;
		$config['base_url'] = base_url()."transporter/driver/index/".$field."/".$keyword;
		$config['total_rows'] = $total;
		$config['per_page'] = $this->config->item("limit_records");

		$this->pagination->initialize($config);
		$this->params["paging"] = $this->pagination->create_links();

		$this->params["title"] = "Manage Driver";
		$this->params["field"] = $field;
		$this->params["keyword"] = $keyword;
		$this->params["offset"] = $offset;
		$this->params["total"] = $total;
		$this->params["data"] = $rows;
		$this->params["row2"] = $row2;
		//ssi company
		if($this->sess->user_company == 356)
		{
			$this->params["car"] = $row_vehicle;
		}
		$this->params["content"] = $this->load->view("transporter/driver/result.php", $this->params, true);
		$this->load->view("templatesess", $this->params);

		$this->dbtransporter->close();
	}

	function add()
	{
		if (! isset($this->sess->user_company)) {
			redirect(base_url());
		}

		$this->params["title"] = "Manage Driver - ADD";
		$this->params['content'] = $this->load->view("transporter/driver/add", $this->params, true);
		$this->load->view("templatesess", $this->params);
	}

	function save() {

	if (! isset($this->sess->user_company)) {
			redirect(base_url());
		}

	$this->dbtransporter = $this->load->database("transporter", true);

	$driver_company = isset($_POST['driver_company']) ? $_POST['driver_company'] : 0;
	$driver_name = isset($_POST['driver_name']) ? $_POST['driver_name'] : "";
	$driver_address = isset($_POST['driver_address']) ? $_POST['driver_address'] : "";
	$driver_phone = isset($_POST['driver_phone']) ? $_POST['driver_phone'] : 0;
	$driver_mobile = isset($_POST['driver_mobile']) ? $_POST['driver_mobile'] : 0;
	$driver_mobile2 = isset($_POST['driver_mobile2']) ? $_POST['driver_mobile2'] : 0;
	$driver_licence = isset($_POST['driver_licence']) ? $_POST['driver_licence'] : "";
	$driver_licence_no = isset($_POST['driver_licence_no']) ? $_POST['driver_licence_no'] : "";
	$driver_sex = isset($_POST['driver_sex']) ? $_POST['driver_sex'] : "";
	$driver_joint_date = isset($_POST['driver_joint_date']) ? $_POST['driver_joint_date'] : "";
	$driver_note = isset($_POST['driver_note']) ? $_POST['driver_note'] : "";
	$driver_licence_expired = isset($_POST['driver_licence_expired']) ? $_POST['driver_licence_expired'] : "";
	$driver_siof = isset($_POST['driver_siof']) ? $_POST['driver_siof'] : "";
	$driver_siof_expired = isset($_POST['driver_siof_expired']) ? $_POST['driver_siof_expired'] : "";
	$driver_group = isset($_POST['driver_group']) ? $_POST['driver_group'] : "";
	$driver_idcard = isset($_POST['driver_idcard']) ? $_POST['driver_idcard'] : "";

	$error = "";
	unset($data);
	$data['driver_company'] = $driver_company;
	$data['driver_name'] = $driver_name;
	$data['driver_address'] = $driver_address;
	$data['driver_phone'] = $driver_phone;
	$data['driver_mobile'] = $driver_mobile;
	$data['driver_mobile2'] = $driver_mobile2;
	$data['driver_licence'] = $driver_licence;
	$data['driver_licence_no'] = $driver_licence_no;
	$data['driver_sex'] = $driver_sex;
	$data['driver_joint_date'] = $driver_joint_date;
	$data['driver_note'] = $driver_note;
	$data['driver_licence_expired'] = $driver_licence_expired;
	$data['driver_siof'] = $driver_siof;
	$data['driver_siof_expired'] = $driver_siof_expired;
	$data['driver_group'] = $driver_group;
	$data['driver_idcard'] = strtoupper($driver_idcard);

	$this->dbtransporter->insert("driver", $data);
	$callback["error"] = false;
	$callback["message"] = "Add Driver Success";
	$callback["redirect"] = base_url()."transporter/driver";

	echo json_encode($callback);
	$this->dbtransporter->close();
	return;

	}

	function edit() {

	if (! isset($this->sess->user_company)) {
			redirect(base_url());
		}

	$driver_id = $this->uri->segment(4);

	if ($driver_id) {
		$this->dbtransporter = $this->load->database("transporter", true);
		$this->dbtransporter->select("*");
		$this->dbtransporter->from("driver");
		$this->dbtransporter->where("driver_id", $driver_id);
		$q = $this->dbtransporter->get();
		if ($q->num_rows == 0) { return; }

		$row = $q->row();
		$this->params['row'] = $row;
		$this->params['title'] = "Edit Driver";
		$this->params['content'] = $this->load->view("transporter/driver/edit", $this->params, true);
		$this->load->view("templatesess", $this->params);
		$this->dbtransporter->close();
	} else {
	$this->dbtransporter->close();
	redirect(base_url()."transporter/driver");
	}

	}

	function update() {

		if (! isset($this->sess->user_company)) {
			redirect(base_url());
		}

		$this->dbtransporter = $this->load->database('transporter', true);

		$driver_company = $this->sess->user_company;
		$driver_id = $this->input->post('driver_id');


		$driver_name = $this->input->post('driver_name');
		$driver_address = $this->input->post('driver_address');
		$driver_phone = $this->input->post('driver_phone');
		$driver_mobile = $this->input->post('driver_mobile');
		$driver_mobile2 = $this->input->post('driver_mobile2');
		$driver_licence = $this->input->post('driver_licence');
		$driver_licence_no = $this->input->post('driver_licence_no');
		$driver_sex = $this->input->post('driver_sex');
		$driver_joint_date = $this->input->post('driver_joint_date');
		$driver_note = $this->input->post('driver_note');

		$driver_licence_expired = $this->input->post('driver_licence_expired');
		$driver_siof = $this->input->post('driver_siof');
		$driver_siof_expired = $this->input->post('driver_siof_expired');
		$driver_group = $this->input->post('driver_group');
		$driver_idcard = $this->input->post('driver_idcard');

		$data = array('driver_company' => $driver_company,
					  'driver_id' => $driver_id,
					  'driver_name' => $driver_name,
					  'driver_idcard' => strtoupper($driver_idcard),
					  'driver_address' => $driver_address,
					  'driver_phone' => $driver_phone,
					  'driver_mobile' => $driver_mobile,
					  'driver_mobile2' => $driver_mobile2,
					  'driver_licence' => $driver_licence,
					  'driver_licence_no' => $driver_licence_no,
					  'driver_sex' => $driver_sex,
					  'driver_joint_date' => $driver_joint_date,
					  'driver_licence_expired' => $driver_licence_expired,
					  'driver_siof' => $driver_siof,
					  'driver_siof_expired' => $driver_siof_expired,
					  'driver_note' => $driver_note,
					  'driver_group' => $driver_group);
		//print_r($data);

		$this->dbtransporter->where('driver_id', $driver_id);
		$this->dbtransporter->update('driver', $data);
		$this->dbtransporter->close();

		redirect (base_url()."transporter/driver", 'refresh');
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
		$q = $this->dbtransporter->get();
		$row = $q->row();

		//select driver image
		$this->dbtransporter->select("*");
		$this->dbtransporter->from("driver_image");
		$this->dbtransporter->where("driver_image_driver_id", $driver_id);
		$q = $this->dbtransporter->get();
		$row_image = $q->row();

		$this->dbtransporter->close();

		$params['row_image'] = $row_image;
		$params['row'] = $row;
		$params["title"] = "Manage Driver - Upload Images";
		$params["driver_id"] = $driver_id;
		$params["error_upload"] = "";
		$html = $this->load->view("transporter/driver/upload_image", $params, true);
		$callback['error'] = false;
		$callback['html'] = $html;
		echo json_encode($callback);

		//$this->load->view("templatesess", $this->params);

	}

	function save_image() {

		$config['upload_path'] = './assets/transporter/images/photo/';
		$config['allowed_types'] = 'gif|jpeg|jpg|png';
		$config['max_size'] = '100';
		$config['max_width'] = '1024';
		$config['max_height'] = '1024';

		$this->load->library('upload', $config);
		$driver_image_driver_id = $this->input->post("driver_id");

		if (!$this->upload->do_upload()) {

			$error = array('error' => $this->upload->display_errors());
			//print_r($error);exit();
			//$this->load->view('transporter/driver/upload_image', $error);
			$this->load->view('transporter/driver/upload_error', $error);
			//redirect(base_url()."transporter/driver");
		}

		else {

			$this->dbtransporter = $this->load->database("transporter", true);
			$data = array('upload_data' => $this->upload->data());

			//print_r($data);exit();

			$driver_image_file_name = $data['upload_data']['file_name'];
			$driver_image_file_type = $data['upload_data']['file_type'];
			$driver_image_file_path = $data['upload_data']['file_path'];
			$driver_image_full_path = $data['upload_data']['full_path'];
			$driver_image_raw_name = $data['upload_data']['raw_name'];
			$driver_image_orig_name = $data['upload_data']['orig_name'];
			$driver_image_client_name = $data['upload_data']['client_name'];
			$driver_image_file_ext = $data['upload_data']['file_ext'];
			$driver_image_file_size = $data['upload_data']['file_size'];
			$driver_image_is_image = $data['upload_data']['is_image'];
			$driver_image_image_width = $data['upload_data']['image_width'];
			$driver_image_image_height = $data['upload_data']['image_height'];
			$driver_image_image_type = $data['upload_data']['image_type'];
			$driver_image_image_size_str = $data['upload_data']['image_size_str'];

			unset($data_insert);
				$data_insert['driver_image_driver_id'] = $driver_image_driver_id;
				$data_insert['driver_image_file_name'] = $driver_image_file_name;
				$data_insert['driver_image_file_path'] = $driver_image_file_path;
				$data_insert['driver_image_full_path'] = $driver_image_full_path;
				$data_insert['driver_image_raw_name'] = $driver_image_raw_name;
				$data_insert['driver_image_orig_name'] = $driver_image_orig_name;
				$data_insert['driver_image_client_name'] = $driver_image_client_name;
				$data_insert['driver_image_file_ext'] = $driver_image_file_ext;
				$data_insert['driver_image_file_size'] = $driver_image_file_size;
				$data_insert['driver_image_is_image'] = $driver_image_is_image;
				$data_insert['driver_image_image_width'] = $driver_image_image_width;
				$data_insert['driver_image_image_height'] = $driver_image_image_height;
				$data_insert['driver_image_image_type'] = $driver_image_image_type;
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

			redirect(base_url()."transporter/driver");
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

	//ssi
	function assign_car()
	{
		if (! isset($this->sess->user_company)) {
			redirect(base_url());
		}
		$this->dbtransporter = $this->load->database("transporter", true);
		$id = $this->uri->segment(4);

		//ssi company
		if($this->sess->user_company == 356)
		{
			$row_vehicle = $this->get_vehicle();
		}

		//amblil driver dan mobil saat ini
		$this->dbtransporter->where("driver_id", $id);
		$q = $this->dbtransporter->get("driver");

		if ($q->num_rows() == 0)
		{
			redirect(base_url() . "transporter/driver/");
		}

		$row = $q->row();

		//Ambil mobil yang driver nya tidak sama dengan id
		//Dan mobil tidak sedang berjalan
		$this->db->order_by("vehicle_no","asc");
		$this->db->where("vehicle_status <>",3);
		$this->db->where("vehicle_company ",$this->sess->user_company);
		$this->db->where("vehicle_group ",$this->sess->user_group);
		$qcar = $this->db->get("vehicle");
		$rowcar = $qcar->result();

		//ssi company
		if($this->sess->user_company == 356)
		{
			$params["car"] = $row_vehicle;
		}

		$params['row'] = $row;
		$params['car'] = $rowcar;
		$html = $this->load->view("transporter/driver/assign_vehicle", $params, true);

		$callback['html'] = $html;
		$callback['error'] = false;

		$this->dbtransporter->cache_delete_all();
		echo json_encode($callback);
	}
	//ssi
	function save_assign_car()
	{
		if (! isset($this->sess->user_company)) {
			redirect(base_url());
		}
		$this->dbtransporter = $this->load->database("transporter", true);
		$id = isset($_POST['id']) ? trim($_POST['id']) : 0;
		$car_before = isset($_POST['car_before']) ? trim($_POST['car_before']) : 0;
		$car_after = isset($_POST['car_after']) ? trim($_POST['car_after']) : "";
		//print_r($car_before);exit();
		if ($id == 0)
		{
			$callback['error'] = true;
			$callback['message'] = "Error, Systen cannot be process your assignment, Driver ID Null";

			echo json_encode($callback);
			return;
		}

		if ($car_after == 0 || $car_after =="")
		{
			$callback['error'] = true;
			$callback['message'] = "Error, Systen cannot be process your assignment, Vehicle ID Null";

			echo json_encode($callback);
			return;
		}

		//Set mobil yang sebelumnya
		//Driver vehicle== 0
		if (isset($car_after) && $car_after != 0)
		{
			unset($set_no_driver);
			$set_no_driver["driver_vehicle"] = 0;
			$this->dbtransporter->where("driver_vehicle", $car_after);
			$this->dbtransporter->update("driver",$set_no_driver);
		}

		//Assign driver untuk mobil yang baru
		unset($set_new_driver);
		$set_new_driver['driver_vehicle'] = $car_after;
		$this->dbtransporter->where("driver_id", $id);
		$this->dbtransporter->update("driver", $set_new_driver);

		$this->dbtransporter->cache_delete_all();
		redirect(base_url() . "transporter/driver/");

	}

	function get_vehicle()
	{
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_company", $this->sess->user_company);
		$qvehicle = $this->db->get("vehicle");
		$row_vehicle = $qvehicle->result();
		return $row_vehicle;
	}

	/* function getvehicle_bydriver()
	{
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_company", $this->sess->user_company);
		$qvehicle = $this->db->get("vehicle");
		$row_vehicle = $qvehicle->result();
		return $row_vehicle;
	} */


	function getVehicle(){
		$driver_id                = $this->input->post('id');
		$driver_name              = $this->input->post('nama');
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
		$this->params['driver_name']  = $driver_name;
		$this->params["data_vehicle"] = $datavehicle;

		$html                     = $this->load->view("transporter/driver/v_forassignvehicle.php", $this->params, true);
		$callback['error']        = false;
		$callback['html']         = $html;
		echo json_encode($callback);
	}

	function assignnow(){
		$driver_id   = $this->input->post('driver_id');
		$driver_name = $this->input->post('driver_name');
		$user_id     = $this->input->post('user_id');
		$vehicle_id  = $this->input->post('vehicle_id');


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
						$getdatauser              = $this->driver_model->get1("webtracking_user", "user_id", $user_id);
						$getdatvehicle              = $this->driver_model->get1("webtracking_vehicle", "vehicle_id", $vehicle_id);
						$getdatadriver              = $this->driver_model->getalldatadbtransporter("driver", "driver_id", $driver_id);

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

	//report
	function report_rfid()
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$this->db->select("vehicle.*, user_name");
		$this->db->order_by("user_name", "asc");
		$this->db->order_by("vehicle_name", "asc");
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where("vehicle_status <>", 3);

		if ($this->sess->user_type == 2)
		{
			$this->db->where("vehicle_user_id", $this->sess->user_id);
			$this->db->or_where("vehicle_company", $this->sess->user_company);
			$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		}
		else
		if ($this->sess->user_type == 3)
		{
			$this->db->where("user_agent", $this->sess->user_agent);
		}

		$this->db->join("user", "vehicle_user_id = user_id", "left outer");
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0)
		{
			redirect(base_url());
		}

		$rows = $q->result();

		$driver_company = $this->sess->user_company;
		$driver_group = $this->sess->user_group;

		$this->dbtransporter = $this->load->database('transporter', true);
		if($driver_group == 0){
			$this->dbtransporter->where("driver_company", $driver_company);
		}else{
			$this->dbtransporter->where("driver_group", $driver_group);
		}
		$this->dbtransporter->where("driver_status", 1);
		$this->dbtransporter->orderby("driver_name","asc");
		$q = $this->dbtransporter->get("driver");
		$driver = $q->result();

		$this->params["vehicles"] = $rows;
		$this->params["drivers"] = $driver;
		$this->params["content"] = $this->load->view('transporter/report/mn_driver_rf_hist', $this->params, true);
		$this->load->view("templatesess", $this->params);
	}

	function report_rfid_result()
	{
		$this->dbalert = $this->load->database("GPS_NORAN_ALERT", true);

		//$vehicle           = $this->input->post("vehicle");
		$startdate         = $this->input->post("startdate");
		$enddate           = $this->input->post("enddate");
		$shour             = $this->input->post("shour");
		$ehour             = $this->input->post("ehour");
		$driver_idcard     = $this->input->post("driver_idcard");

		$sdate             = date("Y-m-d H:i:s", strtotime($startdate . " " . $shour . ":00"));
		$edate             = date("Y-m-d H:i:s", strtotime($enddate . " " . $ehour . ":59"));

			/*

			if ($vehicle != 0)
			{
				$this->dbalert->where("device", $vehicle);
			}*/

			$this->dbalert->where("item", $driver_idcard);
			$this->dbalert->where("datetime >=", $sdate);
			$this->dbalert->where("datetime <=", $edate);
			$this->dbalert->where("message", "31003300"); //khusus alert RF ID
			$this->dbalert->order_by("datetime","desc");
			$q = $this->dbalert->get("webtracking_gps_alert");
			$rows = $q->result();

			$drivers = $this->getDriver();
			$vehicles = $this->get_vehicle();

			$params['data']    = $rows;
			$params['drivers'] = $drivers;
			//$params['vehicle'] = $vehicle;
			$params['vehicles'] = $vehicles;
			$html              = $this->load->view("transporter/report/list_result_driver_rf_hist", $params, true);
			$callback['error'] = false;
			$callback['html']  = $html;
			echo json_encode($callback);
	}

	function getDriver()
	{
		$this->dbtransporter = $this->load->database("transporter", true);
		$this->dbtransporter->where('driver_company', $this->sess->user_company);
		$q = $this->dbtransporter->get("driver");
		$rows = $q->result();
		return $rows;

	}



}

/* End of file driver.php */
/* Location: ./system/application/controllers/transporter/driver.php */
