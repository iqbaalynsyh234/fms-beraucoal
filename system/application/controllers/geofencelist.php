<?php
include "base.php";

class Geofencelist extends Base {

	function __construct()
	{
		parent::Base();
		$this->load->helper('common_helper');
		$this->load->library('email');
	}
	//geofence
	function index(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		
		$this->params['sortby'] = "geofence_id";
		$this->params['orderby'] = "desc";
		$this->params['title'] = "Geofence(live) List";
				
		$this->params["content"] =  $this->load->view('geofencelive/geofence_list', $this->params, true);	
		$this->load->view("templatesess", $this->params);	
	}
	
	function search_geofence(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$this->db = $this->load->database($this->sess->user_dblive, TRUE);
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$status = isset($_POST['status']) ? $_POST['status'] : "";
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "geofence_name";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "asc";
		$company = isset($_POST['company']) ? $_POST['company'] : "";
		$parent = isset($_POST['parent']) ? $_POST['parent'] : "";		
		$company_id = 0;
		
		$error = "";
		
		/*if ($keyword == "")
		{
			$error .= "- Please Input Keyword!! \n";	
		}*/
		
		if ($error != "")
		{
			$callback['error'] = true;	
			$callback['message'] = $error;	
			
			echo json_encode($callback);			
			return;
		}
			
			
		$this->db->select("geofence_id,geofence_name,geofence_vehicle,geofence_created,geofence_user,geofence_speed");
		$this->db->order_by($sortby, $orderby);	
		$this->db->group_by("geofence_name");	
		$this->db->where("geofence_status", 1);
		$this->db->where("geofence_user", $this->sess->user_id);	
		
		switch($field)
		{
			case "geofence_name":
				$this->db->where("geofence_name LIKE '%".$keyword."%'", null);				
			break;
		}
		$q = $this->db->get("geofence");
		$rows = $q->result();
		
		$this->load->library("pagination1");
		
		$config['uri_segment'] = 3;
		
		$this->pagination1->initialize($config);
	
		$this->params["paging"] = $this->pagination1->create_links();
		$this->params["offset"] = $offset;
		//$this->params["total"] = $total;
		$this->params["data"] = $rows;
		$this->params['sortby'] = $sortby;
		$this->params['orderby'] = $orderby;
		
		$html = $this->load->view('geofencelive/geofence_list_result', $this->params, true);
		
		$callback['html'] = $html;
		
				
		echo json_encode($callback);
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
	
	function edit_geofence(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
	
		$id = $this->uri->segment(3);
		$this->db = $this->load->database($this->sess->user_dblive, TRUE);
		$this->db->select("geofence_name,geofence_id,geofence_speed");
		$this->db->limit(1);
		$this->db->where("geofence_id", $id);
		$qr = $this->db->get("geofence");
		$row = $qr->row();
		
		$params['row'] = $row;	

		$callback['html'] = $this->load->view("geofencelive/geofence_edit", $params, true);
		$callback['error'] = false;	
				
		echo json_encode($callback);
	}
	
	function save_geofence(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$this->db = $this->load->database($this->sess->user_dblive, TRUE);
		$id = isset($_POST['id']) ? $_POST['id'] : 0;
		$name = isset($_POST['name']) ? trim($_POST['name']) : "";
		$speed = isset($_POST['speed']) ? trim($_POST['speed']) : "";
		
		$error = "";
		
		if ($name == "")
		{
			$error .= "- Please fill Name ! \n";	
		}
		
		if ($speed == "")
		{
			$error .= "- Please fill Speed Limit ! \n";	
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
		
		if ($id > 0)
		{
			
			$data['geofence_name'] = $name;
			$data['geofence_speed'] = $speed;
			
			$this->db->limit(1);
			$this->db->where("geofence_id",$id);
			$this->db->update("geofence",$data);
			
			$callback['error'] = false;
			$callback['message'] = "Edit Data Success";
			echo json_encode($callback);
				
			return;
		}

	}
	
	
}	