<?php
include "base.php";

class Geofencedatalistlive extends Base {

	function __construct()
	{
		parent::Base();
		$this->load->helper('common_helper');
		$this->load->helper('email');
		$this->load->library('email');
		$this->load->model("dashboardmodel");
		$this->load->helper('common');
	}

	function index()
	{
		$user_parent     = $this->sess->user_parent;
		$privilegecode   = $this->sess->user_id_role;

		$this->params['sortby'] = "group_name";
		$this->params['orderby'] = "asc";

		$this->params['title'] = "Geofence Data List (live)";

		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "geofence_id";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "desc";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$offset=0;
		$this->db = $this->load->database($this->sess->user_dblive, TRUE);
		$this->db->select("geofence_id,geofence_name,geofence_vehicle,geofence_created,geofence_type,geofence_user,geofence_speed,geofence_speed_muatan,
						   geofence_speed_alias, geofence_speed_muatan_alias");
		$this->db->order_by($sortby, $orderby);
		//$this->db->group_by("geofence_name");
		$this->db->where("geofence_status", 1);
		//sementara hardcode dulu khusus geofence list akun root fms
			if ($privilegecode == 1) {
				//$this->db->where("geofence_user", $user_parent);
				$this->db->where("geofence_user", 4408);
			}elseif ($privilegecode == 3) {
				//$this->db->where("geofence_user", $user_parent);
				$this->db->where("geofence_user", 4408);
			}elseif ($privilegecode == 4) {
				//$this->db->where("geofence_user", $user_parent);
				$this->db->where("geofence_user", 4408);
			}else {
				//$this->db->where("geofence_user", $this->sess->user_id);
				$this->db->where("geofence_user", 4408);
			}

		$q = $this->db->get("geofence");
		$rows = $q->result();
		$total = count($rows);


		//get data user
		$this->db = $this->load->database("default", TRUE);
		$this->db->select("user_id,user_name");
		$this->db->where("user_status", 1);
		$qusr = $this->db->get("user");
		$rows_user = $qusr->result();

		$this->load->library("pagination1");

		$config['uri_segment'] = 3;
		$config['total_rows']  = $total;
		$config['per_page']    = $this->config->item("limit_records");

		$this->pagination1->initialize($config);
		$this->params["offset"]         = $offset;
		$this->params["total"]          = $total;
		$this->params["data"]           = $rows;
		$this->params["ruser"]          = $rows_user;
		$this->params['code_view_menu'] = "configuration";
		$this->params["privilegecode"]  = $privilegecode;

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/geofencedatalistlive/v_geofencedata_list', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		}elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/geofencedatalistlive/v_geofencedata_list', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		}elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/geofencedatalistlive/v_geofencedata_list', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		}elseif ($privilegecode == 5) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/geofencedatalistlive/v_geofencedata_list', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
		}elseif ($privilegecode == 6) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/geofencedatalistlive/v_geofencedata_list', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
		}else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/geofencedatalistlive/v_geofencedata_list', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
	}

	function edit($id)
	{
		$id = $this->uri->segment(3);

		$this->dblive = $this->load->database($this->sess->user_dblive, TRUE);
		$this->dblive->select("geofence_name,geofence_id,geofence_created,geofence_speed,geofence_speed_muatan,geofence_type,
							   geofence_speed_alias,geofence_speed_muatan_alias");
		$this->dblive->limit(1);
		$this->dblive->where("geofence_id", $id);
		$qr = $this->dblive->get("geofence");
		$row = $qr->row();

		$this->params["row"] = $row;

		$this->params['code_view_menu'] = "configuration";

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
		$this->params["content"]        = $this->load->view('newdashboard/geofencedatalistlive/v_geofencedata_edit', $this->params, true);
		$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
	}

	function save()
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$usersite = $this->sess->user_company;
		$this->db = $this->load->database($this->sess->user_dblive, TRUE);
		$id = isset($_POST['id']) ? $_POST['id'] : 0;
		$name = isset($_POST['name']) ? trim($_POST['name']) : "";
		$speed = isset($_POST['speed']) ? trim($_POST['speed']) : 0;
		$speed_muatan = isset($_POST['speed_muatan']) ? trim($_POST['speed_muatan']) : 0;

		$speed_alias = isset($_POST['speed_alias']) ? trim($_POST['speed_alias']) : 0;
		$speed_muatan_alias = isset($_POST['speed_muatan_alias']) ? trim($_POST['speed_muatan_alias']) : 0;

		$type = isset($_POST['type']) ? trim($_POST['type']) : "";
		$error = "";

		if ($name == "")
		{
			$error .= "- Please fill Name ! \n";
		}
		if ($type == "")
		{
			$error .= "- Please Choose Type ! \n";
		}

		if ($error != "")
		{
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}

		unset($data);

		$data['geofence_name'] = $name;
		$data['geofence_speed'] = $speed;
		$data['geofence_speed_muatan'] = $speed_muatan;
		$data['geofence_speed_alias'] = $speed_alias;
		$data['geofence_speed_muatan_alias'] = $speed_muatan_alias;
		$data['geofence_type'] = $type;
		if ($id > 0)
		{
			$data['geofence_name'] = $name;
			$data['geofence_speed'] = $speed;
			$data['geofence_speed_muatan'] = $speed_muatan;
			$data['geofence_type'] = $type;

			$this->db->limit(1);
			$this->db->where("geofence_id",$id);
			$this->db->update("geofence",$data);

			$callback['error'] = false;
			$callback['message'] = "Edit Data Success";
			echo json_encode($callback);

			return;
		}

	}

	function delete_geofence($id){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$this->db = $this->load->database($this->sess->user_dblive, TRUE);
		$data["geofence_status"] = 2;
		$this->db->where("geofence_id", $id);
		if($this->db->update("geofence", $data)){

			$callback['message'] = "Data has been deleted";
			$callback['error'] = false;
		}else{
			$callback['message'] = "Failed delete data";
			$callback['error'] = true;
		}
		echo json_encode($callback);
	}



}
