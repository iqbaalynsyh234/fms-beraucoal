<?php
include "base.php";

class Destinationmaster extends Base {

	function Destinationmaster()
	{
		parent::Base();
		$this->load->model("gpsmodel");
		$this->load->model("vehiclemodel");
		$this->load->model("configmodel");
		$this->load->model("historymodel");
		$this->load->model("dashboardmodel");
    $this->load->model("m_destmaster");
		$this->load->model("m_poipoolmaster");
	}

  function index(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$this->params['title']          = "Destination Master";

		$user_id                        = $this->sess->user_id;
		$user_id_fix                    = $user_id;
		$driver_company                 = $this->sess->user_company;
		$driver_group                   = $this->sess->user_group;

		//GET DATA FROM DB
		$where                          = '$this->db->where("dest_creator_id", $user_id_fix)';
		$mastervehicle                  = $this->m_poipoolmaster->getmastervehicle();
		$this->params['mastervehicle']  = $mastervehicle;
		$this->params['destmaster']     = $this->m_destmaster->getalldata("webtracking_destination_master", $user_id_fix, $where);

		$this->dbtransporter            = $this->load->database('transporter', true);
		if($this->sess->user_company == 356)
		{
			$row_vehicle = $this->get_vehicle();
		}

		if($this->sess->user_group == 0){
			$this->dbtransporter->where("driver_company", $driver_company);
		}else{
			$this->dbtransporter->where("driver_group", $driver_group);
		}

		$this->dbtransporter->where("driver_status", 1);
		$this->dbtransporter->orderby("driver_name","asc");
		$qtotal                      = $this->dbtransporter->get("driver");
		$rows                        = $qtotal->result();
		$this->params["driver"]      = $rows;

		// echo "<pre>";
		// var_dump($this->params["destmaster"]);die();
		// echo "<pre>";
		$this->params['code_view_menu'] = "configuration";

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
		$this->params["content"]        = $this->load->view('newdashboard/destinationmaster/v_destmaster', $this->params, true);
		$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
  }

	function savedestmaster(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$user_id              = $this->sess->user_id;
		$user_name            = $this->sess->user_name;
		$user_company         = $this->sess->user_company;
		$dest_vehicle         = explode(".", $this->input->post('dest_vehicle'));
		$dest_vehicle_fix     = $dest_vehicle[0];
		$dest_vehicle_no      = $dest_vehicle[1];
		$dest_driver          = explode(".", $this->input->post('dest_driver'));
		$dest_driver_id       = $dest_driver[0];
		$dest_driver_name     = $dest_driver[1];
		$destname             = $this->input->post('destname');
		$dest_endshowing_date = $this->input->post('dest_endshowing_date');
		$latitude             = $this->input->post('latitude');
		$longitude            = $this->input->post('longitude');
		$addressfix           = $this->input->post('addressfix');

		$data = array(
			"dest_creator_id"      => $user_id,
			"dest_creator_name"    => $user_name,
			"dest_company_id"      => $user_company,
			"dest_vehicle_device"  => $dest_vehicle_fix,
			"dest_vehicle_no"      => $dest_vehicle_no,
			"dest_driver_id"       => $dest_driver_id,
			"dest_driver_name"     => $dest_driver_name,
			"dest_name"            => $destname,
			"dest_endshowing_date" => date("Y-m-d", strtotime($dest_endshowing_date)),
			"dest_lat"             => $latitude,
			"dest_lng"             => $longitude,
			"dest_address"         => $addressfix,
			"dest_created_date"    => date("Y-m-d H:i:s")
		);

		// echo "<pre>";
		// var_dump($data);die();
		// echo "<pre>";

		$insert = $this->m_poipoolmaster->insert_data("webtracking_destination_master", $data);
			if ($insert) {
				$this->session->set_flashdata('notif', 'Destination successfully created');
				redirect('destinationmaster');
			}else {
				$this->session->set_flashdata('notif', 'Destination failed insert');
				redirect('destinationmaster');
			}
	}

	function dest_destmasteredit($id){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$this->params['data']          = $this->m_destmaster->getalldatabydestid("webtracking_destination_master", "dest_id", $id);
		$mastervehicle                 = $this->m_poipoolmaster->getmastervehicle();
		$this->params['mastervehicle'] = $mastervehicle;
		$driver_company                = $this->sess->user_company;
		$driver_group                  = $this->sess->user_group;

		$this->dbtransporter           = $this->load->database('transporter', true);
		if($this->sess->user_company == 356)
		{
			$row_vehicle = $this->get_vehicle();
		}

		if($this->sess->user_group == 0){
			$this->dbtransporter->where("driver_company", $driver_company);
		}else{
			$this->dbtransporter->where("driver_group", $driver_group);
		}

		$this->dbtransporter->where("driver_status", 1);
		$this->dbtransporter->orderby("driver_name","asc");
		$qtotal                      = $this->dbtransporter->get("driver");
		$rows                        = $qtotal->result();
		$this->params["driver"]      = $rows;
		$this->params['code_view_menu']  = "configuration";


		// echo "<pre>";
		// var_dump($this->params['data']);die();
		// echo "<pre>";

		$this->params["header"]      = $this->load->view('dashboard/header', $this->params, true);
		$this->params["sidebar"]     = $this->load->view('dashboard/sidebar', $this->params, true);
		$this->params["chatsidebar"] = $this->load->view('dashboard/chatsidebar', $this->params, true);
		$this->params["content"]     = $this->load->view('dashboard/destinationmaster/v_destmaster_edit', $this->params, true);
		$this->load->view("dashboard/template_dashboard_report", $this->params);
	}

	function updatedestmaster(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$user_id              = $this->sess->user_id;
		$user_name            = $this->sess->user_name;
		$user_company         = $this->sess->user_company;
		$id                   = $this->input->post('id');
		$dest_vehicle         = explode(".", $this->input->post('dest_vehicle'));
		$dest_vehicle_fix     = $dest_vehicle[0];
		$dest_vehicle_no      = $dest_vehicle[1];
		$dest_driver          = explode(".", $this->input->post('dest_driver'));
		$dest_driver_id       = $dest_driver[0];
		$dest_driver_name     = $dest_driver[1];
		$destname             = $this->input->post('destname');
		$dest_endshowing_date = $this->input->post('dest_endshowing_date');
		$latitude             = $this->input->post('latitude');
		$longitude            = $this->input->post('longitude');
		$addressfix           = $this->input->post('addressfix');

		$data = array(
			"dest_creator_id"      => $user_id,
			"dest_creator_name"    => $user_name,
			"dest_company_id"      => $user_company,
			"dest_vehicle_device"  => $dest_vehicle_fix,
			"dest_vehicle_no"      => $dest_vehicle_no,
			"dest_driver_id"       => $dest_driver_id,
			"dest_driver_name"     => $dest_driver_name,
			"dest_name"            => $destname,
			"dest_endshowing_date" => date("Y-m-d", strtotime($dest_endshowing_date)),
			"dest_lat"             => $latitude,
			"dest_lng"             => $longitude,
			"dest_address"         => $addressfix,
			"dest_created_date"    => date("Y-m-d H:i:s")
		);

		// echo "<pre>";
		// var_dump($data);die();
		// echo "<pre>";

		$update = $this->m_destmaster->update_date("webtracking_destination_master", "dest_id", $id, $data);
			if ($update) {
				$this->session->set_flashdata('notif', 'Destination successfully updated');
				redirect('destinationmaster');
			}else {
				$this->session->set_flashdata('notif', 'Destination failed updated');
				redirect('destinationmaster');
			}
	}

	function delete(){
		$iddelete = $this->input->post('iddelete');
		$data = array(
			"dest_flag" => 1
		);
		$delete = $this->m_destmaster->delete_data("webtracking_destination_master", "dest_id", $iddelete, $data);
			if ($delete) {
				$this->session->set_flashdata('notif', 'Destination successfully deleted');
				redirect('destinationmaster');
			}else {
				$this->session->set_flashdata('notif', 'Destination failed deleted');
				redirect('destinationmaster');
			}
	}

	function searchforreport(){
		$userid            = $this->sess->user_id;
		$user_company      = $this->sess->user_company;
		$report_vehicle    = $this->input->post("report_vehicle");
		// $vehicle_a         = $report_vehicle[0];
		// $vehicle_b         = $report_vehicle[1];
		$report_driver     = $this->input->post("report_driver");
		// $driver_a          = $report_driver[0];
		// $driver_b          = $report_driver[1];
		$report_startdate  = date("Y-m-d", strtotime($this->input->post("report_startdate")));
		$report_enddate    = date("Y-m-d", strtotime($this->input->post("report_enddate")));

		$params['data']    = $this->m_destmaster->getforreport("destination_master", $userid, $user_company, $report_vehicle, $report_driver, $report_startdate, $report_enddate);
		// echo "<pre>";
		// var_dump($params['data']);die();
		// echo "<pre>";
		$html              = $this->load->view('dashboard/destinationmaster/v_dest_report', $params, true);
		$callback['html']  = $html;
		echo json_encode($callback);
	}

}
