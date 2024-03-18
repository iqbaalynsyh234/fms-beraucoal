<?php
include "base.php";

class Tms extends Base {

	function __construct()
	{
		parent::Base();
		$this->load->helper('common_helper');
		$this->load->helper('email');
		$this->load->library('email');
		$this->load->model("dashboardmodel");
    $this->load->model("m_tms");
		$this->load->helper('common');
	}

  function index()
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$this->params['title'] = "TMS";
		$user_id               = $this->sess->user_id;
		$user_dblive           = $this->sess->user_dblive;
		$dataticket            = $this->m_tms->getAllticketformaps("tms_ticket", "ticket_creator", $user_id);
		$datafix            	 = array();

		// echo "<pre>";
		// var_dump($dataticket);die();
		// echo "<pre>";

		for ($i=0; $i < sizeof($dataticket); $i++) {
			$device             = explode("@", $dataticket[$i]['ticket_vehicle_device']);
			$device0            = $device[0];
			$device1            = $device[1];

			$getdata[]          = $this->m_poipoolmaster->getLastPosition("webtracking_gps", $user_dblive, $device0);
				array_push($datafix, array(
				 "technician_id"          => $dataticket[$i]['ticket_technician_id'],
				 "technician_name"        => $dataticket[$i]['ticket_technician_name'],
				 "vehicle_id"             => $dataticket[$i]['ticket_vehicle_id'],
				 "vehicle_device"         => $dataticket[$i]['ticket_vehicle_device'],
				 "vehicle_no"             => $dataticket[$i]['ticket_vehicle_no'],
				 "vehicle_name"           => $dataticket[$i]['ticket_vehicle_name'],
				 "vehicle_company"        => $dataticket[$i]['ticket_vehicle_company'],
				 "vehicle_autocheck" 	 		=> $getdata[$i][0]['vehicle_autocheck']
				));
		}

		$throwdatatoview = array();
		for ($loop=0; $loop < sizeof($datafix); $loop++) {
			$jsonnya[$loop] = json_decode($datafix[$loop]['vehicle_autocheck'], true);

			array_push($throwdatatoview, array(
				"ticket_status"          => $dataticket[$loop]['ticket_status'],
				"ticket_name_number"     => $dataticket[$loop]['ticket_name_number'],
				"technician_id"          => $dataticket[$loop]['ticket_technician_id'],
				"technician_name"        => $dataticket[$loop]['ticket_technician_name'],
				"vehicle_id"             => $dataticket[$loop]['ticket_vehicle_id'],
				"vehicle_device"         => $dataticket[$loop]['ticket_vehicle_device'],
				"vehicle_no"             => $dataticket[$loop]['ticket_vehicle_no'],
				"vehicle_name"           => $dataticket[$loop]['ticket_vehicle_name'],
				"vehicle_company"        => $dataticket[$loop]['ticket_vehicle_company'],
				"auto_status"            => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$loop]['auto_status']),
				"auto_last_update"       => date("d F Y H:i:s", strtotime($jsonnya[$loop]['auto_last_update'])),
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

		$this->params["datafix"]  	 = $throwdatatoview;
		// echo "<pre>";
		// var_dump($dataticket);die();
		// echo "<pre>";
		$this->params["header"]      = $this->load->view('tms/header', $this->params, true);
		$this->params["sidebar"]     = $this->load->view('tms/sidebar', $this->params, true);
		$this->params["chatsidebar"] = $this->load->view('tms/chatsidebar', $this->params, true);
		$this->params["content"]     = $this->load->view('tms/v_tms_dashboard', $this->params, true);
		$this->load->view("tms/template_dashboard", $this->params);
	}

  function gardu(){
    $user_id                   = $this->sess->user_id;
    $this->params['title']     = "TMS";
    $this->params['datagardu'] = $this->m_tms->getAllGardu("tms_gardu", "gardu_creator_id", $user_id);
		// echo "<pre>";
		// var_dump($this->params['datagardu']);die();
		// echo "<pre>";

		$this->params["header"]      = $this->load->view('tms/header', $this->params, true);
		$this->params["sidebar"]     = $this->load->view('tms/sidebar', $this->params, true);
		$this->params["chatsidebar"] = $this->load->view('tms/chatsidebar', $this->params, true);
		$this->params["content"]     = $this->load->view('tms/gardu/v_gardu', $this->params, true);
		$this->load->view("tms/template_dashboard", $this->params);
  }

	function customer(){
    $user_id                   = $this->sess->user_id;
    $this->params['title']     = "TMS";
    $this->params['datacustomer'] = $this->m_tms->getAllCustomer("tms_customer", "webtracking_tms_customer_creator", $user_id);
		// echo "<pre>";
		// var_dump($this->params['datagardu']);die();
		// echo "<pre>";

		$this->params["header"]      = $this->load->view('tms/header', $this->params, true);
		$this->params["sidebar"]     = $this->load->view('tms/sidebar', $this->params, true);
		$this->params["chatsidebar"] = $this->load->view('tms/chatsidebar', $this->params, true);
		$this->params["content"]     = $this->load->view('tms/customer/v_customer', $this->params, true);
		$this->load->view("tms/template_dashboard", $this->params);
  }

	function savesubstationmaster(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$user_id      = $this->sess->user_id;
		$user_name    = $this->sess->user_name;
		$user_company = $this->sess->user_company;
		$gardu_name   = $this->input->post('gardu_name');
		$latitude     = $this->input->post('latitude');
		$longitude    = $this->input->post('longitude');
		$addressfix   = $this->input->post('addressfix');

		$data = array(
			"gardu_creator_id"   => $user_id,
			"gardu_creator_name" => $user_name,
			"gardu_company_id"   => $user_company,
			"gardu_name"         => $gardu_name,
			"gardu_lat"          => $latitude,
			"gardu_lng"          => $longitude,
			"gardu_address"      => $addressfix,
			"gardu_created_date" => date("Y-m-d H:i:s")
		);

		// echo "<pre>";
		// var_dump($data);die();
		// echo "<pre>";

		$insert = $this->m_tms->insert_data("webtracking_tms_gardu", $data);
			if ($insert) {
				$this->session->set_flashdata('notif', 'Substation Master data successfully inserted');
				redirect('tms/gardu');
			}else {
				$this->session->set_flashdata('notif', 'Substation Master data failed insert');
				redirect('tms/gardu');
			}
	}

	function savecustomer(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$user_id        = $this->sess->user_id;
		$user_name      = $this->sess->user_name;
		$user_company   = $this->sess->user_company;
		$customer_name  = $this->input->post('customer_name');
		$customer_phone = $this->input->post('customer_phone');
		$latitude       = $this->input->post('latitude');
		$longitude      = $this->input->post('longitude');
		$addressfix     = $this->input->post('addressfix');

		$data = array(
			"webtracking_tms_customer_name"    => $customer_name,
			"webtracking_tms_customer_phone"   => $customer_phone,
			"webtracking_tms_customer_lat"     => $latitude,
			"webtracking_tms_customer_lng"     => $longitude,
			"webtracking_tms_customer_address" => $addressfix,
			"webtracking_tms_customer_creator" => $user_id,
			"created_date"                     => date("Y-m-d H:i:s")
		);

		// echo "<pre>";
		// var_dump($data);die();
		// echo "<pre>";

		$insert = $this->m_tms->insert_data("webtracking_tms_customer", $data);
			if ($insert) {
				$this->session->set_flashdata('notif', 'Customer data successfully inserted');
				redirect('tms/customer');
			}else {
				$this->session->set_flashdata('notif', 'Customer data failed insert');
				redirect('tms/customer');
			}
	}

	function tms_customer_edit($id){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$user_id 	       = $this->sess->user_id;
		$user_level      = $this->sess->user_level;
		$user_company    = $this->sess->user_company;
		$user_subcompany = $this->sess->user_subcompany;
		$user_group      = $this->sess->user_group;
		$user_subgroup   = $this->sess->user_subgroup;
		$user_id_fix     = $user_id;
		//GET DATA FROM DB
		$this->params['data'] = $this->m_tms->getalldatabycustid("webtracking_tms_customer", "webtracking_tms_customer_id", $id);

		// echo "<pre>";
		// var_dump($this->params['data']);die();
		// echo "<pre>";

		$this->params["header"]      = $this->load->view('tms/header', $this->params, true);
		$this->params["sidebar"]     = $this->load->view('tms/sidebar', $this->params, true);
		$this->params["chatsidebar"] = $this->load->view('tms/chatsidebar', $this->params, true);
		$this->params["content"]     = $this->load->view('tms/customer/v_customer_edit', $this->params, true);
		$this->load->view("dashboard/template_dashboard", $this->params);
	}

	function updatecustomer(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$user_id        = $this->sess->user_id;
		$user_name      = $this->sess->user_name;
		$user_company   = $this->sess->user_company;
		$id             = $this->input->post('id');
		$customer_name  = $this->input->post('customer_name');
		$customer_phone = $this->input->post('customer_phone');
		$latitude       = $this->input->post('latitude');
		$longitude      = $this->input->post('longitude');
		$addressfix     = $this->input->post('addressfix');

		$data = array(
			"webtracking_tms_customer_name"    => $customer_name,
			"webtracking_tms_customer_phone"   => $customer_phone,
			"webtracking_tms_customer_lat"     => $latitude,
			"webtracking_tms_customer_lng"     => $longitude,
			"webtracking_tms_customer_address" => $addressfix
		);

		// echo "<pre>";
		// var_dump($data);die();
		// echo "<pre>";

		$update = $this->m_tms->update_data_all("webtracking_tms_customer", "webtracking_tms_customer_id", $id, $data);
			if ($update) {
				$this->session->set_flashdata('notif', 'Customer data successfully updated');
				redirect('tms/customer');
			}else {
				$this->session->set_flashdata('notif', 'Customer data failed updated');
				redirect('tms/customer');
			}
	}

	function deletecustomer(){
		$iddelete = $this->input->post('iddelete');
		$data = array(
			"webtracking_tms_customer_flag" => 1
		);
		$delete = $this->m_tms->delete_data("webtracking_tms_customer", "webtracking_tms_customer_id", $iddelete, $data);
			if ($delete) {
				$this->session->set_flashdata('notif', 'Customer data successfully deleted');
				redirect('tms/customer');
			}else {
				$this->session->set_flashdata('notif', 'Customer data failed deleted');
				redirect('tms/customer');
			}
	}

	function tms_gardu_edit($id){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$user_id 	       = $this->sess->user_id;
		$user_level      = $this->sess->user_level;
		$user_company    = $this->sess->user_company;
		$user_subcompany = $this->sess->user_subcompany;
		$user_group      = $this->sess->user_group;
		$user_subgroup   = $this->sess->user_subgroup;
		$user_id_fix     = $user_id;
		//GET DATA FROM DB
		$this->params['data'] = $this->m_tms->getalldatabygarduid("webtracking_tms_gardu", "gardu_id", $id);

		// echo "<pre>";
		// var_dump($this->params['data']);die();
		// echo "<pre>";

		$this->params["header"]      = $this->load->view('tms/header', $this->params, true);
		$this->params["sidebar"]     = $this->load->view('tms/sidebar', $this->params, true);
		$this->params["chatsidebar"] = $this->load->view('tms/chatsidebar', $this->params, true);
		$this->params["content"]     = $this->load->view('tms/gardu/v_gardu_edit', $this->params, true);
		$this->load->view("dashboard/template_dashboard", $this->params);
	}

	function updategardumaster(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$user_id      = $this->sess->user_id;
		$user_name    = $this->sess->user_name;
		$user_company = $this->sess->user_company;
		$id           = $this->input->post('id');
		$gardu_name   = $this->input->post('gardu_name');
		$latitude     = $this->input->post('latitude');
		$longitude    = $this->input->post('longitude');
		$addressfix   = $this->input->post('addressfix');

		$data = array(
			"gardu_creator_id"   => $user_id,
			"gardu_creator_name" => $user_name,
			"gardu_company_id"   => $user_company,
			"gardu_name"         => $gardu_name,
			"gardu_lat"          => $latitude,
			"gardu_lng"          => $longitude,
			"gardu_address"      => $addressfix
		);

		// echo "<pre>";
		// var_dump($data);die();
		// echo "<pre>";

		$update = $this->m_tms->update_date("webtracking_tms_gardu", "gardu_id", $id, $data);
			if ($update) {
				$this->session->set_flashdata('notif', 'Gardu Master data successfully updated');
				redirect('tms/gardu');
			}else {
				$this->session->set_flashdata('notif', 'Gardu Master data failed updated');
				redirect('tms/gardu');
			}
	}

		function delete(){
			$iddelete = $this->input->post('iddelete');
			$data = array(
				"gardu_flag" => 1
			);
			$delete = $this->m_tms->delete_data("webtracking_tms_gardu", "gardu_id", $iddelete, $data);
				if ($delete) {
					$this->session->set_flashdata('notif', 'Gardu Master data successfully deleted');
					redirect('tms/gardu');
				}else {
					$this->session->set_flashdata('notif', 'Gardu Master data failed deleted');
					redirect('tms/gardu');
				}
		}

		function technician(){
			$user_id                          = $this->sess->user_id;
			$user_company                     = $this->sess->user_company;
	    $this->params['title']          	= "TMS";
	    $this->params['datatechnician'] 	= $this->m_tms->getAlltechnician("tms_technisian", "technician_company", $user_company);
			$this->params['datavehicle'] 	    = $this->m_poipoolmaster->getmastervehicle();


			// echo "<pre>";
			// var_dump($this->params['datavehicle']);die();
			// echo "<pre>";

			$this->params["header"]      = $this->load->view('tms/header', $this->params, true);
			$this->params["sidebar"]     = $this->load->view('tms/sidebar', $this->params, true);
			$this->params["chatsidebar"] = $this->load->view('tms/chatsidebar', $this->params, true);
			$this->params["content"]     = $this->load->view('tms/technisian/v_technisian', $this->params, true);
			$this->load->view("tms/template_dashboard", $this->params);
		}

		function savetechnician(){
			if (! isset($this->sess->user_type))
			{
				redirect(base_url());
			}

			$user_id            = $this->sess->user_id;
			$user_name          = $this->sess->user_name;
			$user_company       = $this->sess->user_company;
			$technician_name    = $this->input->post('technician_name');
			$technician_phone   = $this->input->post('technician_phone');
			$technician_email   = $this->input->post('technician_email');
			$technician_license = $this->input->post('technician_license');
			$technician_sex     = $this->input->post('technician_sex');
			$technician_address = $this->input->post('technician_address');

			$data = array(
				"technician_name"    => $technician_name,
				"technician_phone"   => $technician_phone,
				"technician_email"   => $technician_email,
				"technician_licence" => $technician_license,
				"technician_sex"     => $technician_sex,
				"technician_address" => $technician_address,
				"technician_company" => $user_company
			);

			// echo "<pre>";
			// var_dump($data);die();
			// echo "<pre>";

			$insert = $this->m_tms->insert_data("webtracking_tms_technisian", $data);
				if ($insert) {
					$this->session->set_flashdata('notif', 'Technician Master data successfully inserted');
					redirect('tms/technician');
				}else {
					$this->session->set_flashdata('notif', 'Technician Master data failed insert');
					redirect('tms/technician');
				}
		}

		function tms_technician_edit($id){
			if (! isset($this->sess->user_type))
			{
				redirect(base_url());
			}

			$user_id 	       = $this->sess->user_id;
			$user_level      = $this->sess->user_level;
			$user_company    = $this->sess->user_company;

			$this->params['data'] = $this->m_tms->getalldatabytechid("webtracking_tms_technisian", "technician_id", $id, $user_company);

			// echo "<pre>";
			// var_dump($this->params['data']);die();
			// echo "<pre>";

			$this->params["header"]      = $this->load->view('tms/header', $this->params, true);
			$this->params["sidebar"]     = $this->load->view('tms/sidebar', $this->params, true);
			$this->params["chatsidebar"] = $this->load->view('tms/chatsidebar', $this->params, true);
			$this->params["content"]     = $this->load->view('tms/technisian/v_technisian_edit', $this->params, true);
			$this->load->view("dashboard/template_dashboard", $this->params);
		}

		function updatetechnician(){
			if (! isset($this->sess->user_type))
			{
				redirect(base_url());
			}

			$user_id            = $this->sess->user_id;
			$user_name          = $this->sess->user_name;
			$user_company       = $this->sess->user_company;
			$id                 = $this->input->post('id');
			$technician_name    = $this->input->post('technician_name');
			$technician_phone   = $this->input->post('technician_phone');
			$technician_email   = $this->input->post('technician_email');
			$technician_license = $this->input->post('technician_license');
			$technician_sex     = $this->input->post('technician_sex');
			$technician_address = $this->input->post('technician_address');

			$data = array(
				"technician_name"    => $technician_name,
				"technician_phone"   => $technician_phone,
				"technician_email"   => $technician_email,
				"technician_licence" => $technician_license,
				"technician_sex"     => $technician_sex,
				"technician_address" => $technician_address
			);

			// echo "<pre>";
			// var_dump($data);die();
			// echo "<pre>";

			$update = $this->m_tms->update_data_all("webtracking_tms_technisian", "technician_id", $id, $data);
				if ($update) {
					$this->session->set_flashdata('notif', 'Technician Master data successfully updated');
					redirect('tms/technician');
				}else {
					$this->session->set_flashdata('notif', 'Technician Master data failed updated');
					redirect('tms/technician');
				}
		}

		function assignvehicletotechnician(){
			$user_company        = $this->sess->user_company;
			$user_id   			     = $this->sess->user_id;
			$id_teknisi          = explode("-", $this->input->post('id_teknisi'));
			$idteknisi           = $id_teknisi[0];
			$vehicle_device_lama = $id_teknisi[1];
			$vehicle_device_baru = $this->input->post('vehicle');

			$datateknisi    = $this->m_tms->getalldatabytechid("webtracking_tms_technisian", "technician_id", $idteknisi, $user_company);
			$isvehicleexist = $this->m_tms->cekisvehicleexist("webtracking_tms_technisian", "technician_vehicle_device", $vehicle_device_baru, $user_company);
				if (sizeof($isvehicleexist) > 0) {
					$this->session->set_flashdata('notif', "Vehicle has been assign to another technician");
					redirect('tms/technician');
				}else {
					if ($vehicle_device_baru == "0000") {
						$textnotif = "Technician now available";
						$parametervehicle = $vehicle_device_lama;
						$forvehicle = array(
							"vehicle_tms"    => "0000"
						);
						$fortechnician = array(
							"technician_vehicle_device"    => $vehicle_device_baru
						);
					}else {
						$textnotif = "Successfully Assign Vehicle to Technician";
						$parametervehicle = $vehicle_device_baru;
						$forvehicle = array(
							"vehicle_tms"    => $datateknisi[0]['technician_id'].'|'.$datateknisi[0]['technician_name'].'|'.$datateknisi[0]['technician_phone']
						);
						$fortechnician = array(
							"technician_vehicle_device"    => $vehicle_device_baru
						);
					}
					$update = $this->m_tms->update_dataforassignvehicle("vehicle", "vehicle_device", "vehicle_user_id", $user_id, $parametervehicle, $forvehicle);
					if ($update) {
						$update2 = $this->m_tms->update_dataforassignvehicle("webtracking_tms_technisian", "technician_id", "technician_company", $user_company, $idteknisi, $fortechnician);
						if ($update2) {
							$this->session->set_flashdata('notif', $textnotif);
							redirect('tms/technician');
						}else {
							$this->session->set_flashdata('notif', $textnotif);
							redirect('tms/technician');
						}
					}else {
						$this->session->set_flashdata('notif', 'Failed Assign Vehicle to Technician');
						redirect('tms/technician');
					}
				}
		}

		function deletetechnician(){
			$iddelete = $this->input->post('iddelete');
			$data = array(
				"technician_status" => 2
			);
			$delete = $this->m_tms->delete_data("webtracking_tms_technisian", "technician_id", $iddelete, $data);
				if ($delete) {
					$this->session->set_flashdata('notif', 'technician Master data successfully deleted');
					redirect('tms/technician');
				}else {
					$this->session->set_flashdata('notif', 'technician Master data failed deleted');
					redirect('tms/technician');
				}
		}

		function ticketing(){
			if (! isset($this->sess->user_type)){
				redirect(base_url());
			}

			$user_id                      = $this->sess->user_id;
			$user_company                 = $this->sess->user_company;
			$this->params['title']        = "TMS";
			$this->params['dataticket']   = $this->m_tms->getAllticket("tms_ticket", "ticket_creator", $user_id);
			$this->params['datagardu']    = $this->m_tms->getAllGardu("tms_gardu", "gardu_creator_id", $user_id);
			$this->params['datacustomer'] = $this->m_tms->getAllCustomer("tms_customer", "webtracking_tms_customer_creator", $user_id);
			// echo "<pre>";
			// var_dump($user_company);die();
			// echo "<pre>";

			$this->params["header"]      = $this->load->view('tms/header', $this->params, true);
			$this->params["sidebar"]     = $this->load->view('tms/sidebar', $this->params, true);
			$this->params["chatsidebar"] = $this->load->view('tms/chatsidebar', $this->params, true);
			$this->params["content"]     = $this->load->view('tms/ticketing/v_ticketing', $this->params, true);
			$this->load->view("tms/template_dashboard", $this->params);
		}

	function searchforticket(){
		$user_id     = $this->sess->user_id;
		$ticket_type = $_POST['ticket_type'];
		$sendId      = $_POST['sendId'];
		$dataarray   = array();

		if ($ticket_type == 0) {
			// CUSTOMER
			$data = $this->m_tms->getalldatabycustid("webtracking_tms_customer", "webtracking_tms_customer_id", $sendId);
				for ($i=0; $i < sizeof($data); $i++) {
					array_push($dataarray, array(
						"id"      => $data[0]['webtracking_tms_customer_id'],
						"name"    => $data[0]['webtracking_tms_customer_name'],
						"phone"   => $data[0]['webtracking_tms_customer_phone'],
						"lat"     => $data[0]['webtracking_tms_customer_lat'],
						"lng"     => $data[0]['webtracking_tms_customer_lng'],
						"address" => $data[0]['webtracking_tms_customer_address']
					));
				}
		}else {
			// GARDU
			$data = $this->m_tms->getalldatabygarduid("webtracking_tms_gardu", "gardu_id", $sendId);
			for ($i=0; $i < sizeof($data); $i++) {
				array_push($dataarray, array(
					"id"      => $data[0]['gardu_id'],
					"name"    => $data[0]['gardu_name'],
					"phone"   => "",
					"lat"     => $data[0]['gardu_lat'],
					"lng"     => $data[0]['gardu_lng'],
					"address" => $data[0]['gardu_address']
				));
			}
		}

		$user_id_fix = $user_id;

		$companyid 			 = $this->uri->segment(3);


		$user_dblive    = $this->sess->user_dblive;
		$datafromdblive = $this->m_tms->getfromdblive("webtracking_gps", $user_dblive);
		$mastervehicle  = $this->m_tms->getmastervehicle();

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

			if ($mastervehicle[$i]['vehicle_tms'] != "0000") {
				$technician  = explode("|", $mastervehicle[$i]['vehicle_tms']);
				$technician0 = $technician[0];
				$technician1 = $technician[1];
				$technician2 = $technician[2];
			}else {
				$technician0 = "Not Assign Yet";
				$technician1 = "Not Assign Yet";
				$technician2 = "Not Assign Yet";
			}

			// print_r($technician);
			// $getdata[] = $this->m_poipoolmaster->getmastervehiclebydevid($device);
			$getdata[]                 = $this->m_poipoolmaster->getLastPosition("webtracking_gps", $user_dblive, $device0);
			// $laspositionfromgpsmodel[] = $this->gpsmodel->GetLastInfo($device0, $device1, true, false, 0, "");
				if (sizeof($getdata[$i]) > 0) {
					// $jsonnya[] = json_decode($getdata[$i][0]['vehicle_autocheck'], true);
							array_push($datafix, array(
  						 "is_update" 						  => "yes",
							 "technician_id"          => $technician0,
							 "technician_name"        => $technician1,
							 "technician_phone"       => $technician2,
							 "vehicle_id"             => $mastervehicle[$i]['vehicle_id'],
		 					 "vehicle_user_id"        => $mastervehicle[$i]['vehicle_user_id'],
		 					 "vehicle_device"         => $mastervehicle[$i]['vehicle_device'],
		 					 "vehicle_no"             => $mastervehicle[$i]['vehicle_no'],
		 					 "vehicle_name"           => $mastervehicle[$i]['vehicle_name'],
		 					 "vehicle_company"        => $mastervehicle[$i]['vehicle_company'],
							 "vehicle_card_no"        => $mastervehicle[$i]['vehicle_card_no'],
		 					 "vehicle_group"          => $mastervehicle[$i]['vehicle_group'],
		 					 "vehicle_subgroup"       => $mastervehicle[$i]['vehicle_subgroup'],
		 					 "vehicle_odometer"       => $mastervehicle[$i]['vehicle_odometer'],
		 					 "vehicle_imei"           => str_replace(array("\n","\r","'","'\'","/", "-"), "", $mastervehicle[$i]['vehicle_imei']),
		 					 "vehicle_dbhistory"      => $mastervehicle[$i]['vehicle_dbhistory'],
		 					 "vehicle_dbhistory_name" => $mastervehicle[$i]['vehicle_dbhistory_name'],
		 					 "vehicle_dbname_live"    => $mastervehicle[$i]['vehicle_dbname_live'],
							 "vehicle_autocheck" 	 		=> $getdata[$i][0]['vehicle_autocheck']
							));
				}else {
					// $jsonnya2[$i] = json_decode($mastervehicle[$i]['vehicle_autocheck'], true);
					array_push($deviceidygtidakada, array(
						"is_update"              => "no",
						"technician_id"          => $technician0,
						"technician_name"        => $technician1,
						"technician_phone"       => $technician2,
						"vehicle_id"             => $mastervehicle[$i]['vehicle_id'],
						"vehicle_user_id"        => $mastervehicle[$i]['vehicle_user_id'],
						"vehicle_device"         => $mastervehicle[$i]['vehicle_device'],
						"vehicle_no"             => $mastervehicle[$i]['vehicle_no'],
						"vehicle_name"           => $mastervehicle[$i]['vehicle_name'],
						"vehicle_company"        => $mastervehicle[$i]['vehicle_company'],
						"vehicle_card_no"        => $mastervehicle[$i]['vehicle_card_no'],
						"vehicle_group"          => $mastervehicle[$i]['vehicle_group'],
						"vehicle_subgroup"       => $mastervehicle[$i]['vehicle_subgroup'],
						"vehicle_odometer"       => $mastervehicle[$i]['vehicle_odometer'],
						"vehicle_imei"           => str_replace(array("\n","\r","'","'\'","/", "-"), "", $mastervehicle[$i]['vehicle_imei']),
						"vehicle_dbhistory"      => $mastervehicle[$i]['vehicle_dbhistory'],
						"vehicle_dbhistory_name" => $mastervehicle[$i]['vehicle_dbhistory_name'],
						"vehicle_dbname_live"    => $mastervehicle[$i]['vehicle_dbname_live'],
						"vehicle_autocheck"      => $mastervehicle[$i]['vehicle_autocheck']
					));
				}
		}

		// echo "<pre>";
		// var_dump($getdata);die();
		// echo "<pre>";

		$datafixbgt = array_merge($datafix, $deviceidygtidakada);
		$throwdatatoview = array();
		for ($loop=0; $loop < sizeof($datafixbgt); $loop++) {
			$jsonnya[$loop] = json_decode($datafixbgt[$loop]['vehicle_autocheck'], true);

			array_push($throwdatatoview, array(
				"is_update" 						 => $datafixbgt[$loop]['is_update'],
				"technician_id"          => $datafixbgt[$loop]['technician_id'],
				"technician_name"        => $datafixbgt[$loop]['technician_name'],
				"technician_phone"       => $datafixbgt[$loop]['technician_phone'],
				"vehicle_id"             => $datafixbgt[$loop]['vehicle_id'],
				"vehicle_user_id"        => $datafixbgt[$loop]['vehicle_user_id'],
				"vehicle_device"         => $datafixbgt[$loop]['vehicle_device'],
				"vehicle_no"             => $datafixbgt[$loop]['vehicle_no'],
				"vehicle_name"           => $datafixbgt[$loop]['vehicle_name'],
				"vehicle_company"        => $datafixbgt[$loop]['vehicle_company'],
				"vehicle_card_no"        => $datafixbgt[$loop]['vehicle_card_no'],
				"vehicle_group"          => $datafixbgt[$loop]['vehicle_group'],
				"vehicle_subgroup"       => $datafixbgt[$loop]['vehicle_subgroup'],
				"vehicle_odometer"       => $datafixbgt[$loop]['vehicle_odometer'],
				"vehicle_imei"           => str_replace(array("\n","\r","'","'\'","/", "-"), "", $datafixbgt[$loop]['vehicle_imei']),
				"vehicle_dbhistory"      => $datafixbgt[$loop]['vehicle_dbhistory'],
				"vehicle_dbhistory_name" => $datafixbgt[$loop]['vehicle_dbhistory_name'],
				"vehicle_dbname_live"    => $datafixbgt[$loop]['vehicle_dbname_live'],
				"auto_status"            => str_replace(array("\n","\r","'","'\'","/", "-"), "", $jsonnya[$loop]['auto_status']),
				"auto_last_update"       => date("d F Y H:i:s", strtotime($jsonnya[$loop]['auto_last_update'])),
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

		echo json_encode(array("code" => 200, "datadblive" => $throwdatatoview, "dataforcompare" => $dataarray));
	}

	function getlastinfonya(){
		$device                    = explode("@", $_POST['device']);
		$device0                   = $device[0];
		$device1                   = $device[1];
		$laspositionfromgpsmodel   = $this->gpsmodel->GetLastInfo($device0, $device1, true, false, 0, "");
		echo json_encode($laspositionfromgpsmodel);
	}

	function saveticket(){
		header("Access-Control-Allow-Headers: Authorization, Content-Type");
		header("Access-Control-Allow-Origin: *");
		header('content-type: application/json; charset=utf-8');
		$obj               = file_get_contents('php://input');
		$dataobject        = json_decode($obj);
		$dataarray         = (array) $dataobject;
		$ticket_type       = $dataarray['ticket_type'];
		$techid            = $dataarray['techid'];
		$devid             = $dataarray['devid'];
		$distance          = $dataarray['distance'];
		$ticket_name       = $dataarray['ticket_name'];
		$customer_id       = $dataarray['customer_name'];
		$substation_id     = $dataarray['substation_name'];
		$duedate           = $dataarray['duedate'];
		$ticket_keterangan = $dataarray['ticket_keterangan'];
		$user_company      = $this->sess->user_company;
		$user_id           = $this->sess->user_id;

		// GET DATA TEKNISI
		$datateknisi  = $this->m_tms->getalldatabytechid("webtracking_tms_technisian", "technician_id", $techid, $user_company);
		$datavehicle  = $this->m_tms->getmastervehiclebydevid($devid);


		// echo "<pre>";
		// var_dump($dataarray);die();
		// echo "<pre>";


		if ($ticket_type == 0) {
			// GET DATA CUSTOMER
			$datacustomer = $this->m_tms->getalldatabycustid("webtracking_tms_customer", "webtracking_tms_customer_id", $customer_id);
			// CUSTOMER
			$dataforsave = array(
				"ticket_name_number"                 => $ticket_name,
				"ticket_type"                        => $ticket_type,
				"ticket_technician_id"               => $techid,
				"ticket_technician_name"             => $datateknisi[0]['technician_name'],
				"ticket_technician_company"          => $datateknisi[0]['technician_company'],
				"ticket_vehicle_id"                  => $devid,
				"ticket_vehicle_device"              => $datavehicle[0]['vehicle_device'],
				"ticket_vehicle_company"             => $datavehicle[0]['vehicle_company'],
				"ticket_vehicle_user_id"             => $datavehicle[0]['vehicle_user_id'],
				"ticket_vehicle_no"                  => $datavehicle[0]['vehicle_no'],
				"ticket_vehicle_name"                => $datavehicle[0]['vehicle_name'],
				"ticket_dist_to_destination"         => $distance,
				"ticket_customer_substation_id"      => $customer_id,
				"ticket_customer_substation_name"    => $datacustomer[0]['webtracking_tms_customer_name'],
				"ticket_customer_substation_lat"     => $datacustomer[0]['webtracking_tms_customer_lat'],
				"ticket_customer_substation_lng"     => $datacustomer[0]['webtracking_tms_customer_lng'],
				"ticket_customer_substation_address" => $datacustomer[0]['webtracking_tms_customer_address'],
				"ticket_duedate"                     => date("Y-m-d", strtotime($duedate)),
				"ticket_keterangan"                  => $ticket_keterangan,
				"ticket_creator"                     => $user_id,
				"ticket_status"                      => "0", // 0 scheduled, 1. on duty, 2. sampai dilokasi (on process), 3. completed
				"ticket_created_date"                => date("Y-m-d H:i:s")
			);
		}else {
			// GET DATA CUSTOMER
			$datasubstation = $this->m_tms->getalldatabygarduid("webtracking_tms_gardu", "gardu_id", $substation_id);
			// SUBSTATION
			$dataforsave = array(
				"ticket_name_number"                 => $ticket_name,
				"ticket_type"                        => $ticket_type,
				"ticket_technician_id"               => $techid,
				"ticket_technician_name"             => $datateknisi[0]['technician_name'],
				"ticket_technician_company"          => $datateknisi[0]['technician_company'],
				"ticket_vehicle_id"                  => $devid,
				"ticket_vehicle_device"              => $datavehicle[0]['vehicle_device'],
				"ticket_vehicle_company"             => $datavehicle[0]['vehicle_company'],
				"ticket_vehicle_user_id"             => $datavehicle[0]['vehicle_user_id'],
				"ticket_vehicle_no"                  => $datavehicle[0]['vehicle_no'],
				"ticket_vehicle_name"                => $datavehicle[0]['vehicle_name'],
				"ticket_dist_to_destination"         => $distance,
				"ticket_customer_substation_id"      => $substation_id,
				"ticket_customer_substation_name"    => $datasubstation[0]['gardu_name'],
				"ticket_customer_substation_lat"     => $datasubstation[0]['gardu_lat'],
				"ticket_customer_substation_lng"     => $datasubstation[0]['gardu_lng'],
				"ticket_customer_substation_address" => $datasubstation[0]['gardu_address'],
				"ticket_duedate"                     => date("Y-m-d", strtotime($duedate)),
				"ticket_keterangan"                  => $ticket_keterangan,
				"ticket_creator"                     => $user_id,
				"ticket_status"                      => "0", // 0 scheduled, 1. on duty, 2. sampai dilokasi (on process), 3. completed
				"ticket_created_date"                => date("Y-m-d H:i:s")
			);
		}

		// echo "<pre>";
		// var_dump($dataforsave);die();
		// echo "<pre>";
		// echo json_encode(array("code" => 200, "msg" => "success", "text" => "success, notif alredy sent to technician"));

		$insert = $this->m_tms->insert_data("webtracking_tms_ticket", $dataforsave);
			if ($insert) {
				$type = $dataforsave['ticket_type'];
					if ($type == 0) {
						$type = "Customer";
					}else {
						$type = "Substation";
					}

				$notenya = $dataforsave['ticket_keterangan'];
					if ($notenya == "") {
						$notenya = "";
					}else {
						$notenya = $dataforsave['ticket_keterangan'];
					}

				$telegram_group = $this->get_telegram_bycompanyid("1798"); // 1798 INI TESTING SAJA DI WEBTRACKING_COMPANY, NANTI DIGANTI

				$message =  urlencode(
							"NOTIFIKASI PEKERJAAN : \n".
							"Kepada : ".$dataforsave['ticket_technician_name']." \n".
							"Ticket Number : ".$dataforsave['ticket_name_number']." \n".
							$type." : ".$dataforsave['ticket_customer_substation_name']." \n".
							"Distance : ".$dataforsave['ticket_dist_to_destination']." Km \n".
							"Due Date : ".date("d-m-Y", strtotime($dataforsave['ticket_duedate']))." \n".
							"Note : ".$notenya."\n".
							"Location : ".$dataforsave['ticket_customer_substation_address']."\n".
							"Coords : https://www.google.com/maps/place/".$dataforsave['ticket_customer_substation_lat'].",".$dataforsave['ticket_customer_substation_lng']
						);
				$this->telegram_direct($telegram_group,$message);
					$from_email   = "noreply.tmslacakmobil@gmail.com";
	        $to_email = "dimas.saputra@lacak-mobil.com";

					$this->load->library('email');

					$config['protocol']     = 'smtp';
					$config['smtp_host']    = 'ssl://smtp.gmail.com';
					$config['smtp_port']    = '465';
					$config['smtp_timeout'] = '7';
					$config['smtp_user']    = $from_email;
					$config['smtp_pass']    = 'Tms_2020';
					$config['charset']      = 'utf-8';
					$config['newline']      = "\r\n";
					$config['mailtype']     = 'html'; // or html
					$config['validation']   = TRUE; // bool whether to validate email or not

					$this->email->initialize($config);

					$this->email->from($from_email, 'Lacak Mobil');
					$this->email->to($to_email);
					$this->email->subject('Notifikasi Pekerjaan');
					// $contenya = $this->load->view('tms/emailtemplate', $dataforsave);
					$contenya = $this->emailtemplate($dataforsave);
					$this->email->message($contenya);

					$this->email->send();
					// echo json_encode(array("code" => 200, "msg" => "success", "text" => "success, notif alredy sent to technician"));
			}else {
				echo json_encode(array("code" => 400, "msg" => "error", "text" => error_get_last()));
			}
	}

	function cekifduplicate(){
			if (isset($_POST['ticket_name_check'])) {
			$ticket_name = $_POST['ticket_name'];
			$getdataifduplicate = $this->m_tms->getdataduplicatename("webtracking_tms_ticket", "ticket_name_number", $ticket_name);
				if (sizeof($getdataifduplicate) > 0) {
					echo json_encode(array("msg" => "taken"));
				}else {
					echo json_encode(array("msg" => "not_taken"));
				}
		}
	}

	function changestatus(){
		$ticketid  = $_POST['ticketid'];
		$statusnow = $_POST['statusnya'];

		if ($statusnow == "") {
			$this->session->set_flashdata('notif', 'Please choose status correctly');
			redirect('tms/ticketing');
		}

		$data = array(
			"ticket_status" => $statusnow
		);

		$update = $this->m_tms->update_data_all("tms_ticket", "ticket_id", $ticketid, $data);
			if ($update) {
				$this->session->set_flashdata('notif', 'Ticket status has been updated');
				redirect('tms/ticketing');
			}else {
				$this->session->set_flashdata('notif', 'Failed change ticket status');
				redirect('tms/ticketing');
			}
	}


	function emailtemplate($dataforsave){
		$mailwarning = $this->config->item('mail_warning');
		//$mailaddress = $this->config->item('mail_address');
		$mailfootnote = $this->config->item('mail_footnote');
		$mailfooter = $this->config->item('mail_footer');
		$type = $dataforsave['ticket_type'];
			if ($type == 0) {
				$type = "Customer";
			}else {
				$type = "Substation";
			}

		$notenya = $dataforsave['ticket_keterangan'];
			if ($notenya == "") {
				$notenya = "";
			}else {
				$notenya = $dataforsave['ticket_keterangan'];
			}

		$msg = "<html><head>".
		$this->config->item('mail_css').
		"</head><body> ".
		$this->config->item('mail_header').
		"Kepada <strong> ".$dataforsave['ticket_technician_name'].", <strong> <br>".
						"Berikut informasi pekerjaan yang memerlukan penanganan segera. <br><br>".
						"<table>".
						"<tr>".
							"<td>Ticket Number</td>".
							"<td>:</td>".
							"<td>".$dataforsave['ticket_name_number']."</td>".
						"</tr>".

						  "<tr>".
						    "<td>".$type."</td>".
						    "<td>:</td>".
						    "<td>".$dataforsave['ticket_customer_substation_name']."</td>".
						  "</tr>".

						  "<tr>".
						    "<td>Distance</td>".
						    "<td>:</td>".
						    "<td>".$dataforsave['ticket_dist_to_destination']." Km</td>".
						  "</tr>".

						  "<tr>".
						    "<td>Due Date</td>".
						    "<td>:</td>".
						    "<td>".date("d-m-Y", strtotime($dataforsave['ticket_duedate']))."</td>".
						  "</tr>".

						  "<tr>".
						    "<td>Location</td>".
						    "<td>:</td>".
						    "<td><a href='https://www.google.com/maps/place/".$dataforsave['ticket_customer_substation_lat'].",".$dataforsave['ticket_customer_substation_lng']."'"." ".">".$dataforsave['ticket_customer_substation_address']."</a></td>".
						  "</tr>".

							"<tr>".
							 "<td>Note</td>".
							 "<td>:</td>".
							 "<td>".$notenya."</td>".
						 "</tr>".
						"</table> <br>".
						$mailwarning.
						$mailfootnote.
						"Demikian perintah kerja ini, harap segera ditindak lanjuti. <br>".
						"E-mail ini dikirim secara otomatis, harap untuk tidak membalas email ini. <br>".
						"Terima kasih.".
						$mailfooter.
						"</body>
						</html>";
		return $msg;
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

		$getdatafromticket = $this->m_tms->getAllticketformapsbyid("tms_ticket", "ticket_vehicle_user_id", $result[0]['vehicle_device'], $user_id);

		// echo "<pre>";
		// var_dump($getdatafromticket);die();
		// echo "<pre>";

		$dataticket = array();
		for ($x=0; $x < sizeof($getdatafromticket); $x++) {
			array_push($dataticket, array(
				"ticket_number" => $getdatafromticket[$x]['ticket_name_number'],
				"ticket_status" => $getdatafromticket[$x]['ticket_status']
			));
		}

		$datadest = array();
		for ($y=0; $y < sizeof($getdatafromticket); $y++) {
			array_push($datadest, array(
				"dest_lat"     => $getdatafromticket[$y]['ticket_customer_substation_lat'],
				"dest_lng"     => $getdatafromticket[$y]['ticket_customer_substation_lng'],
				"dest_address" => $getdatafromticket[$y]['ticket_customer_substation_address']
			));
		}

		$datafix = array();
		for ($i=0; $i < sizeof($result); $i++) {
			$jsonnya[$i] = json_decode($result[$i]['vehicle_autocheck']);

			array_push($datafix, array(
  			 "ticket_technician_name" => $getdatafromticket[0]['ticket_technician_name'],
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

		$this->params['vehicle']    = $datafix;
		$this->params['dataticket'] = $dataticket;
		$this->params['datadest']   = $datadest;

		// echo "<pre>";
		// var_dump($this->params['datadest']);die();
		// echo "<pre>";

		$this->params["header"]      = $this->load->view('tms/header', $this->params, true);
		$this->params["sidebar"]     = $this->load->view('tms/sidebar', $this->params, true);
		$this->params["chatsidebar"] = $this->load->view('tms/chatsidebar', $this->params, true);
		$this->params["content"]     = $this->load->view('tms/ticketing/v_tracking', $this->params, true);
		$this->load->view("tms/template_dashboard", $this->params);
	}

	function get_telegram_bycompanyid($id)
	{
		//get telegram group by company
		$this->db = $this->load->database("default",TRUE);
		$this->db->select("company_id,company_telegram_cron");
		$this->db->where("company_id",$id);
		$qcompany = $this->db->get("company");
		$rcompany = $qcompany->row();
		if(count($rcompany)>0){
			$telegram_group = $rcompany->company_telegram_cron;
		}else{
			$telegram_group = 0;
		}
		return $telegram_group;
	}

	function telegram_direct($groupid,$message)
		{
			// print_r("group id nya ". $groupid."\n");
				error_reporting(E_ALL);
				ini_set('display_errors', 1);

				$url = "http://lacak-mobil.com/telegram/telegram_directpost";

				$data = array("id" => $groupid, "message" => $message);
				$data_string = json_encode($data);

				$ch = curl_init($url);
				curl_setopt($ch, CURLOPT_HEADER, false);
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($data_string)));
				$result = curl_exec($ch);

				if ($result === FALSE) {
						die("Curl failed: " . curL_error($ch));
				}
				// echo "<pre>";
				// var_dump($result);die();
				// echo "<pre>";
				echo $result;
				echo curl_getinfo($ch, CURLINFO_HTTP_CODE);

		}


}
