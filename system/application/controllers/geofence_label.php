<?php
include "base.php";

class Geofence_label extends Base {

	function __construct()
	{
		parent::Base();	
	}
	
	function index(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$this->params['sortby'] = "geofence_name";
		$this->params['orderby'] = "asc";
		$this->params['title'] = "Geofence List";
				
		$this->params["content"] =  $this->load->view('geofence/geofencelabel_list', $this->params, true);	;		
		$this->load->view("templatesess", $this->params);	
	}
	
	function search(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "geofence_name";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "asc";
		$type = isset($_POST['type']) ? $_POST['type'] : "";
		
		$this->db->select("geofence_id,geofence_name,geofence_type,geofence_user");
		$this->db->order_by($sortby, $orderby);
		$this->db->group_by("geofence_name");	
		$this->db->where("geofence_status", 1);
		$this->db->where("geofence_user", $this->sess->user_id);
		switch($field)
		{
			case "name":
				$this->db->where("geofence_name LIKE '%".$keyword."%'", null);				
			break;			
			case "type":
				$this->db->where("geofence_type", $type);
			break;		
		}
			
		//$q = $this->db->get("geofence", $this->config->item("limit_records"));
		$q = $this->db->get("geofence");
		$rows = $q->result();
		$total = count($rows);
		/* //print_r(count($rows));exit();
		$this->db->select("count(*) as total");
		//$this->db->select("geofence_id,geofence_name,geofence_type,geofence_user");
		//$this->db->group_by("geofence_name");		
		$this->db->where("geofence_status", 1);
		$this->db->where("geofence_user", $this->sess->user_id);
		switch($field)
		{
			case "name":
				$this->db->where("geofence_name LIKE '%".$keyword."%'", null);				
			break;			
			case "type":
				$this->db->where("geofence_type", $type);
			break;		
		}
			
		$qt = $this->db->get("geofence");
		$rt = $qt->row();
		$total = $rt->total;
		 */

		$this->load->library("pagination1");
		
		$config['uri_segment'] = 3;
		$config['total_rows'] = $total;
		$config['per_page'] = $this->config->item("limit_records");
		
		$this->pagination1->initialize($config);
		
		$this->params["paging"] = $this->pagination1->create_links();
		$this->params["offset"] = $offset;
		$this->params["total"] = $total;
		$this->params["data"] = $rows;
		$this->params['sortby'] = $sortby;
		$this->params['orderby'] = $orderby;

		$html = $this->load->view('geofence/geofencelabel_result', $this->params, true);
		
		$callback['html'] = $html;
		$callback['total'] = $total;
				
				
		echo json_encode($callback);
	}
	
	function edit(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
	
		$id = $this->uri->segment(3);

		$this->db->select("geofence_id,geofence_name,geofence_type,geofence_user");
		$this->db->where("geofence_id", $id);
		$q = $this->db->get("geofence");
		$row = $q->row();
		
		$params['row'] = $row;		
		
		$callback['html'] = $this->load->view("geofence/geofencelabel_edit", $params, true);
		$callback['error'] = false;	
				
		echo json_encode($callback);
	}
	
	function save()
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$id = isset($_POST['id']) ? $_POST['id'] : 0;
		$name = isset($_POST['name']) ? trim($_POST['name']) : "";
		$oldname = isset($_POST['oldname']) ? trim($_POST['oldname']) : "";
		$type = isset($_POST['type']) ? trim($_POST['type']) : "";
		$status = isset($_POST['status']) ? trim($_POST['status']) : 1;
		$error = "";
		if ($name == "")
		{
			$error = "Please Fill Geofence Name !";
			$callback['error'] = true;
            $callback['message'] = $error;
            
            echo json_encode($callback);
            return;
		}
		if ($oldname == "")
		{
			$error = "Old Name Not Available!";
			$callback['error'] = true;
            $callback['message'] = $error;
            
            echo json_encode($callback);
            return;
		}
		if ($type == "")
		{
			$error = "Please Fill Geofence Type !";
			$callback['error'] = true;
            $callback['message'] = $error;
            
            echo json_encode($callback);
            return;
		}

		unset($data);
		$data['geofence_name'] = $name;
		$data['geofence_type'] = $type;
		$data['geofence_status'] = $status;
		
		if ($id)
		{
			$this->db->where("geofence_name", $oldname);
			$this->db->where("geofence_user", $this->sess->user_id);
			$this->db->update("geofence", $data);			
			
			$callback['error'] = false;
			$callback['message'] = "Geofence Label Successfully Updated";
			$callback['redirect'] = base_url()."geofence_label";
			
			echo json_encode($callback);
			
			return;
		}else{
			
			$error = "Goefence tidak tersedia !";
			
			$callback['error'] = true;
			$callback['message'] = "Geofence Label Successfully Updated";
			$callback['redirect'] = base_url()."geofence_label";
			
			echo json_encode($callback);
			
			return;
		}
	}
	
	function delete($id)
	{
		
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		//select geofence name
		$this->db->select("geofence_id,geofence_name");
		$this->db->where("geofence_id", $id);
		$q = $this->db->get("geofence");
		$row = $q->row();
		if (isset($row) && (count($row) > 0))
		{
			$name = $row->geofence_name;
			$data["geofence_status"] = 2;
			$this->db->select("geofence_id,geofence_name,geofence_type,geofence_user");
			$this->db->where("geofence_name", $name);
			$this->db->where("geofence_user", $this->sess->user_id);
			if($this->db->update("geofence", $data)){
				$callback['message'] = "Data has been deleted";
				$callback['error'] = false;	
			}else{
				$callback['message'] = "Failed delete data";
				$callback['error'] = true;	
			}
		}else{
				$callback['message'] = "No Data Geofence";
				$callback['error'] = true;	
		}
		
		echo json_encode($callback);
	}
}