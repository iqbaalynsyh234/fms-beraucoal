<?php
include "base.php";

class Poolmaster extends Base {

	function __construct()
	{
		parent::Base();
		$this->load->model("dashboardmodel");
		$this->load->model("m_poipoolmaster");
	}

	function index(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$this->params['title']      = "Pool Master";

		$user_id 	       = $this->sess->user_id;
		$user_id_fix     = $user_id;
		//GET DATA FROM DB
		$where     = "";

		$where = '$this->db->where("poi_creator_id", $user_id_fix)';


		$this->params['poolmaster']     = $this->m_poipoolmaster->getalldata("webtracking_poi_poolmaster", $user_id_fix, $where);
		$this->params['code_view_menu'] = "configuration";

		// echo "<pre>";
		// var_dump($this->params['poolmaster']);die();
		// echo "<pre>";

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
		$this->params["content"]        = $this->load->view('newdashboard/poimaster/v_poi_poolmaster', $this->params, true);
		$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
	}

	function savepoolmaster(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$user_id      = $this->sess->user_id;
		$user_name    = $this->sess->user_name;
		$user_company = $this->sess->user_company;
		$poolname     = $this->input->post('poolname');
		$latitude     = $this->input->post('latitude');
		$longitude    = $this->input->post('longitude');
		$addressfix   = $this->input->post('addressfix');

		$data = array(
			"poi_creator_id"   => $user_id,
			"poi_creator_name" => $user_name,
			"poi_company_id"   => $user_company,
			"poi_name"         => $poolname,
			"poi_lat"          => $latitude,
			"poi_lng"          => $longitude,
			"poi_address"      => $addressfix,
			"poi_created_date" => date("Y-m-d H:i:s")
		);

		// echo "<pre>";
		// var_dump($data);die();
		// echo "<pre>";

		$insert = $this->m_poipoolmaster->insert_data("webtracking_poi_poolmaster", $data);
			if ($insert) {
				$this->session->set_flashdata('notif', 'Pool Master data successfully inserted');
				redirect('poolmaster');
			}else {
				$this->session->set_flashdata('notif', 'Pool Master data failed insert');
				redirect('poolmaster');
			}
	}

	function poi_poolmasteredit($id){
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
		$where     = "";

		if($user_level == 1){
			$where = '$this->db->where("vehicle_user_id", $user_id_fix)';
		}else if($user_level == 2){
			$where = '$this->db->where("vehicle_company", $user_company)';
		}else if($user_level == 3){
			$where = '$this->db->where("vehicle_subcompany", $user_subcompany)';
		}else if($user_level == 4){
			$where = '$this->db->where("vehicle_group", $user_group)';
		}else if($user_level == 5){
			$where = '$this->db->where("vehicle_subgroup", $user_subgroup)';
		}else{
			$where = '$this->db->where("vehicle_no",99999)';
		}

		$this->params['data'] = $this->m_poipoolmaster->getalldatabypoiid("webtracking_poi_poolmaster", $where, "poi_id", $id);

		// echo "<pre>";
		// var_dump($this->params['data']);die();
		// echo "<pre>";
		$this->params['code_view_menu'] = "configuration";


		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
		$this->params["content"]        = $this->load->view('newdashboard/poimaster/v_poi_poolmasteredit', $this->params, true);
		$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
	}

	function updatepoolmaster(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$user_id      = $this->sess->user_id;
		$user_name    = $this->sess->user_name;
		$user_company = $this->sess->user_company;
		$id 		      = $this->input->post('id');
		$poolname     = $this->input->post('poolname');
		$latitude     = $this->input->post('latitude');
		$longitude    = $this->input->post('longitude');
		$addressfix   = $this->input->post('addressfix');

		$data = array(
			"poi_creator_id"   => $user_id,
			"poi_creator_name" => $user_name,
			"poi_company_id"   => $user_company,
			"poi_name"         => $poolname,
			"poi_lat"          => $latitude,
			"poi_lng"          => $longitude,
			"poi_address"      => $addressfix
		);

		// echo "<pre>";
		// var_dump($data);die();
		// echo "<pre>";

		$update = $this->m_poipoolmaster->update_date("webtracking_poi_poolmaster", "poi_id", $id, $data);
			if ($update) {
				$this->session->set_flashdata('notif', 'Pool Master data successfully updated');
				redirect('poolmaster');
			}else {
				$this->session->set_flashdata('notif', 'Pool Master data failed updated');
				redirect('poolmaster');
			}
	}

	function delete(){
		$iddelete = $this->input->post('iddelete');
		$data = array(
			"poi_flag" => 1
		);
		$delete = $this->m_poipoolmaster->delete_data("webtracking_poi_poolmaster", "poi_id", $iddelete, $data);
			if ($delete) {
				$this->session->set_flashdata('notif', 'Pool Master data successfully deleted');
				redirect('poolmaster');
			}else {
				$this->session->set_flashdata('notif', 'Pool Master data failed deleted');
				redirect('poolmaster');
			}
	}

}
