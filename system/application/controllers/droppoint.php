<?php
include "base.php";

class Droppoint extends Base {

	function __construct()
	{
		parent::Base();
		$this->load->helper('common_helper');
	}
	//table transporter_droppoint
	function index(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		
		$this->params['sortby'] = "droppoint_id";
		$this->params['orderby'] = "desc";
		$this->params['title'] = "Config Droppoint";
		
		//Get company
		$row_company = $this->get_company_bylogin();
		$this->params['rcompany'] = $row_company;
		
		//Get Distrep
		$row_distrep = $this->get_distrep_bycreator();
		$this->params['rdistrep'] = $row_distrep;
		
		//Get Parent
		$row_parent = $this->get_parent_bycreator();
		$this->params['rparent'] = $row_parent;
				
		$this->params["content"] =  $this->load->view('transporter/droppoint/droppoint_list', $this->params, true);	
		$this->load->view("templatesess", $this->params);	
	}
	
	//table = droppoint_distrep
	function distrep(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		
		$this->params['sortby'] = "distrep_id";
		$this->params['orderby'] = "desc";
		$this->params['title'] = "Config Distrep";
				
		$this->params["content"] =  $this->load->view('transporter/droppoint/distrep_list', $this->params, true);	
		$this->load->view("templatesess", $this->params);	
	}
	
	//table = droppoint_parent
	function dataparent(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		
		$this->params['sortby'] = "parent_id";
		$this->params['orderby'] = "desc";
		$this->params['title'] = "Config Parent";
				
		$this->params["content"] =  $this->load->view('transporter/droppoint/parent_list', $this->params, true);	
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
		$status = isset($_POST['status']) ? $_POST['status'] : "";
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "droppoint_id";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "desc";
		$company = isset($_POST['company']) ? $_POST['company'] : "";
		$parent = isset($_POST['parent']) ? $_POST['parent'] : "";		
		
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by($sortby, $orderby);	
		$this->dbtransporter->where("droppoint_flag", 0);
		$this->dbtransporter->where("droppoint_creator", $this->sess->user_id);
		switch($field)
		{
			case "droppoint_name":
				$this->dbtransporter->where("droppoint_name LIKE '%".$keyword."%'", null);				
			break;
			case "droppoint_code":
				$this->dbtransporter->where("droppoint_code LIKE '%".$keyword."%'", null);				
			break;
			
		}
		
		$this->dbtransporter->join("droppoint_distrep", "droppoint_distrep=distrep_id", "left");
		$this->dbtransporter->join("droppoint_parent", "distrep_parent=parent_id", "left");
		$q = $this->dbtransporter->get("droppoint", $this->config->item('limit_records'), $offset);
		$rows = $q->result();
		$total = count($rows);
		
		$this->dbtransporter->order_by($sortby, $orderby);	
		$this->dbtransporter->where("distrep_flag", 0);
		$this->dbtransporter->where("droppoint_creator", $this->sess->user_id);
		switch($field)
		{
			case "droppoint_name":
				$this->dbtransporter->where("droppoint_name LIKE '%".$keyword."%'", null);				
			break;
			case "droppoint_code":
				$this->dbtransporter->where("droppoint_code LIKE '%".$keyword."%'", null);				
			break;
			
		}
		
		$this->dbtransporter->join("droppoint_distrep", "droppoint_distrep=distrep_id", "left");
		$this->dbtransporter->join("droppoint_parent", "distrep_parent=parent_id", "left");
        $qp = $this->dbtransporter->get("droppoint");
		
		$this->load->library("pagination1");
		
		$config['uri_segment'] = 3;
		$config['total_rows'] = $total;
		$config['per_page'] = 20;
		
		$this->pagination1->initialize($config);
		
		//Get Parent
		$row_parent = $this->get_parent_bycreator();
		$this->params['rparent'] = $row_parent;
		
		//Get distrep
		$row_distrep = $this->get_distrep_bycreator();
		$this->params['rdistrep'] = $row_distrep;
		
		//Get company
		$row_company = $this->get_company_bylogin();
		$this->params['rcompany'] = $row_company;
	
		$this->params["paging"] = $this->pagination1->create_links();
		$this->params["offset"] = $offset;
		$this->params["total"] = $total;
		$this->params["data"] = $rows;
		$this->params['sortby'] = $sortby;
		$this->params['orderby'] = $orderby;
		
		$html = $this->load->view('transporter/droppoint/droppoint_list_result', $this->params, true);
		
		$callback['html'] = $html;
		$callback['total'] = $total;
				
		echo json_encode($callback);
	}
	
	function search_distrep(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$status = isset($_POST['status']) ? $_POST['status'] : "";
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "distrep_id";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "desc";
		$company = isset($_POST['company']) ? $_POST['company'] : "";
		$parent = isset($_POST['parent']) ? $_POST['parent'] : "";		
		
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by($sortby, $orderby);	
		$this->dbtransporter->where("distrep_flag", 0);
		switch($field)
		{
			case "distrep_name":
				$this->dbtransporter->where("distrep_name LIKE '%".$keyword."%'", null);				
			break;
			case "distrep_code":
				$this->dbtransporter->where("distrep_code LIKE '%".$keyword."%'", null);				
			break;
			
		}
		
		$this->dbtransporter->join("droppoint_parent", "distrep_parent=parent_id", "left");
		$q = $this->dbtransporter->get("droppoint_distrep", $this->config->item('limit_records'), $offset);
		$rows = $q->result();
		$total = count($rows);
		
		//Hitung total
		$this->dbtransporter->order_by($sortby, $orderby);	
		$this->dbtransporter->where("distrep_flag", 0);
		switch($field)
		{
			case "distrep_name":
				$this->dbtransporter->where("distrep_name LIKE '%".$keyword."%'", null);				
			break;
			case "distrep_code":
				$this->dbtransporter->where("distrep_code LIKE '%".$keyword."%'", null);				
			break;
			
		}
		
		$this->dbtransporter->join("droppoint_parent", "distrep_parent=parent_id", "left");
        $qp = $this->dbtransporter->get("droppoint_distrep");
		
		$this->load->library("pagination1");
		
		$config['uri_segment'] = 3;
		$config['total_rows'] = $total;
		$config['per_page'] = 20;
		
		//Get company
		$row_company = $this->get_company_bylogin();
		$this->params['rcompany'] = $row_company;
		
		//Get parent
		$row_parent = $this->get_parent_bycreator();
		$this->params['rparent'] = $row_parent;
		
		$this->pagination1->initialize($config);
	
		$this->params["paging"] = $this->pagination1->create_links();
		$this->params["offset"] = $offset;
		$this->params["total"] = $total;
		$this->params["data"] = $rows;
		$this->params['sortby'] = $sortby;
		$this->params['orderby'] = $orderby;
		
		$html = $this->load->view('transporter/droppoint/distrep_list_result', $this->params, true);
		
		$callback['html'] = $html;
		$callback['total'] = $total;
				
		echo json_encode($callback);
	}
	
	function search_dataparent(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$status = isset($_POST['status']) ? $_POST['status'] : "";
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "parent_id";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "desc";
		$company = isset($_POST['company']) ? $_POST['company'] : "";
		
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by($sortby, $orderby);	
		$this->dbtransporter->where("parent_flag", 0);
		$this->dbtransporter->where("parent_creator", $this->sess->user_id);
		switch($field)
		{
			case "parent_name":
				$this->dbtransporter->where("parent_name LIKE '%".$keyword."%'", null);				
			break;
			case "parent_code":
				$this->dbtransporter->where("parent_code LIKE '%".$keyword."%'", null);				
			break;
			
		}
		
		$q = $this->dbtransporter->get("droppoint_parent", $this->config->item('limit_records'), $offset);
		$rows = $q->result();
		$total = count($rows);
		
		//Hitung total budget
		$this->dbtransporter->order_by($sortby, $orderby);	
		$this->dbtransporter->where("parent_flag", 0);
		$this->dbtransporter->where("parent_creator", $this->sess->user_id);
		switch($field)
		{
			case "parent_name":
				$this->dbtransporter->where("parent_name LIKE '%".$keyword."%'", null);				
			break;
			case "parent_code":
				$this->dbtransporter->where("parent_code LIKE '%".$keyword."%'", null);				
			break;
			
		}
		
		$qp = $this->dbtransporter->get("droppoint_parent");
		
		$this->load->library("pagination1");
		
		$config['uri_segment'] = 3;
		$config['total_rows'] = $total;
		$config['per_page'] = 20;
		
		//Get company
		$row_company = $this->get_company_bylogin();
		$this->params['rcompany'] = $row_company;
		
		$this->pagination1->initialize($config);
	
		$this->params["paging"] = $this->pagination1->create_links();
		$this->params["offset"] = $offset;
		$this->params["total"] = $total;
		$this->params["data"] = $rows;
		$this->params['sortby'] = $sortby;
		$this->params['orderby'] = $orderby;
		
		$html = $this->load->view('transporter/droppoint/parent_list_result', $this->params, true);
		
		$callback['html'] = $html;
		$callback['total'] = $total;
				
		echo json_encode($callback);
	}
	
	function add(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
	
		$id = $this->uri->segment(3);
		
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->where("droppoint_id", $id);
		$qr = $this->dbtransporter->get("droppoint");
		$row = $qr->row();
		
		$params['row'] = $row;
		
		//Get Parent
		$row_parent = $this->get_parent_bycreator();
		$params['rparent'] = $row_parent;
		
		//Get distrep
		$row_distrep = $this->get_distrep_bycreator();
		$params['rdistrep'] = $row_distrep;
		
		//Get company
		$row_company = $this->get_company_bylogin();
		$params['rcompany'] = $row_company;
		
		//Get geofence //berdasarkan login 
		$row_geofence = $this->get_geofence_bylogin();
		$params['rgeofence'] = $row_geofence;
		
		$callback['html'] = $this->load->view("transporter/droppoint/droppoint_add", $params, true);
		$callback['error'] = false;	
				
		echo json_encode($callback);
	}
	
	function add_distrep(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
	
		$id = $this->uri->segment(3);
		
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->where("distrep_id", $id);
		$qr = $this->dbtransporter->get("droppoint_distrep");
		$row = $qr->row();
		
		$params['row'] = $row;
		
		//Get Parent
		$row_parent = $this->get_parent_bycreator();
		$params['rparent'] = $row_parent;
		
		$callback['html'] = $this->load->view("transporter/droppoint/distrep_add", $params, true);
		$callback['error'] = false;	
				
		echo json_encode($callback);
	}
	
	function add_dataparent(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
	
		$id = $this->uri->segment(3);
		
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->where("parent_id", $id);
		$qr = $this->dbtransporter->get("droppoint_parent");
		$row = $qr->row();
		
		$params['row'] = $row;
		
		//Get company
		$row_company = $this->get_company_bylogin();
		$params['rcompany'] = $row_company;
		
		$callback['html'] = $this->load->view("transporter/droppoint/parent_add", $params, true);
		$callback['error'] = false;	
				
		echo json_encode($callback);
	}
	
	function edit(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
	
		$id = $this->uri->segment(3);
		
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->where("droppoint_id", $id);
		$qr = $this->dbtransporter->get("droppoint");
		$row = $qr->row();
		
		$params['row'] = $row;
		
		//Get Parent
		$row_parent = $this->get_parent_bycreator();
		$params['rparent'] = $row_parent;
		
		//Get distrep
		$row_distrep = $this->get_distrep_bycreator();
		$params['rdistrep'] = $row_distrep;
		
		//Get company
		$row_company = $this->get_company_bylogin();
		$params['rcompany'] = $row_company;
		
		//Get geofence //berdasarkan login 
		$row_geofence = $this->get_geofence_bylogin();
		$params['rgeofence'] = $row_geofence;
		
		$callback['html'] = $this->load->view("transporter/droppoint/droppoint_edit", $params, true);
		$callback['error'] = false;	
				
		echo json_encode($callback);
	}
	
	function edit_distrep(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
	
		$id = $this->uri->segment(3);
		
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->where("distrep_id", $id);
		$qr = $this->dbtransporter->get("droppoint_distrep");
		$row = $qr->row();
		
		$params['row'] = $row;
		
		//Get Parent
		$row_parent = $this->get_parent_bycreator();
		$params['rparent'] = $row_parent;
		
		$callback['html'] = $this->load->view("transporter/droppoint/distrep_edit", $params, true);
		$callback['error'] = false;	
				
		echo json_encode($callback);
	}
	
	function edit_dataparent(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
	
		$id = $this->uri->segment(3);
		
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->where("parent_id", $id);
		$qr = $this->dbtransporter->get("droppoint_parent");
		$row = $qr->row();
		
		$params['row'] = $row;	

		//Get company
		$row_company = $this->get_company_bylogin();
		$params['rcompany'] = $row_company;	
		
		$callback['html'] = $this->load->view("transporter/droppoint/parent_edit", $params, true);
		$callback['error'] = false;	
				
		echo json_encode($callback);
	}
	
	function save(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		
		$id = isset($_POST['id']) ? $_POST['id'] : 0;
		$name = isset($_POST['name']) ? trim($_POST['name']) : "";
		$code = isset($_POST['code']) ? trim($_POST['code']) : "";
		$distrep = isset($_POST['distrep']) ? $_POST['distrep'] : "";
		$geofence = isset($_POST['geofence']) ? $_POST['geofence'] : "";
		$geofence_id = isset($_POST['geofence_id']) ? $_POST['geofence_id'] : "";
		$target_time = isset($_POST['target_time']) ? trim($_POST['target_time']) : "";
		$target_date = isset($_POST['target_date']) ? trim($_POST['target_date']) : "";
		$target_type = isset($_POST['target_type']) ? trim($_POST['target_type']) : "";
		$type = isset($_POST['type']) ? trim($_POST['type']) : "";
		$flag = isset($_POST['flag']) ? trim($_POST['flag']) : 0;
		$hour = isset($_POST['hour']) ? trim($_POST['hour']) : "";
		$minute = isset($_POST['minute']) ? trim($_POST['minute']) : "";
		$company = isset($_POST['company']) ? trim($_POST['company']) : $this->sess->user_company;
		$creator = isset($_POST['creator']) ? trim($_POST['creator']) : $this->sess->user_id;
		$creator_datetime = isset($_POST['creator_datetime']) ? trim($_POST['creator_datetime']) : date("Y-m-d H:i:s");
		
		$error = "";
		
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		
			if ($type == "")
			{
				$error .= "- Please Select Type \n";	
			}
			
			if ($name == "")
			{
				$error .= "- Please fill Name \n";	
			}
			
			if ($code == "")
			{
				$error .= "- Please fill Code \n";	
			}else{
			
				$this->dbtransporter->where("droppoint_code", $code);
				$this->dbtransporter->where("droppoint_type", $type);
				$this->dbtransporter->where("droppoint_flag", 0);
				$this->dbtransporter->where("droppoint_creator", $this->sess->user_id);
				$q = $this->dbtransporter->get("droppoint");
				
				if ($q->num_rows() > 0)
				{
					$row = $q->row();
					if ($row->droppoint_id != $id)
					{
						$error .= "- Code already exist !! \n";
					}
				}
			}
			
			if ($geofence_id == "")
			{
				$error .= "- Please Select Geofence \n";	
			}else{
				//select geofence_name
				$this->db->select("geofence_name");
				$this->db->where("geofence_id", $geofence_id);
				$qg = $this->db->get("geofence");
				$row_g = $qg->row();
				if((count($row_g) > 0)){
					$geofence = $row_g->geofence_name;
				}else{
					$error .= "- NO Data Geofence Name !! \n";
				}
				
			}
			
			if ($geofence == "")
			{
				$error .= "- Please Select Geofence \n";	
			}
			
			if ($distrep == "")
			{
				$error .= "- Please Select Distrep \n";	
			}
			
			if ($hour == "")
			{
				$error .= "- Please fill Hour \n";	
			}
			
			if ($minute == "")
			{
				$error .= "- Please fill Minute \n";	
			}
			
			$target_time = $hour.":".$minute.":"."00";
			
			$target_time_real = date('H:i:s',strtotime($target_time));
			
		if ($error != "")
		{
			$callback['error'] = true;	
			$callback['message'] = $error;	
			
			echo json_encode($callback);			
			return;
		}
		
		unset($data);
		
		$data['droppoint_name'] = $name;
		$data['droppoint_code'] = $code;
		$data['droppoint_distrep'] = $distrep;
		$data['droppoint_geofence'] = $geofence;
		$data['droppoint_geofence_id'] = $geofence_id;
		$data['droppoint_target_time'] = $target_time_real;
		$data['droppoint_company'] = $company;
		$data['droppoint_creator'] = $creator;
		$data['droppoint_creator_datetime'] = $creator_datetime;
		$data['droppoint_flag'] = $flag;
		
		if ($id > 0)
		{
			$data['droppoint_id'] = $id;
			$this->dbtransporter->where("droppoint_id",$id);
			$this->dbtransporter->update("droppoint",$data);
			
			$callback['error'] = false;
			$callback['message'] = "Edit Data Success";
			echo json_encode($callback);
				
			return;
		}else{
			
			$this->dbtransporter->insert("droppoint", $data);
				
			$callback['error'] = false;
			$callback['message'] = "Insert Data Succes";
			echo json_encode($callback);
			
			return;
		}

	}
	
	function save_distrep(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		
		$id = isset($_POST['id']) ? $_POST['id'] : 0;
		$name = isset($_POST['name']) ? trim($_POST['name']) : "";
		$code = isset($_POST['code']) ? trim($_POST['code']) : "";
		$parent = isset($_POST['parent']) ? $_POST['parent'] : 0;
		$company = isset($_POST['company']) ? trim($_POST['company']) : 0;
		$vehicle_company = isset($_POST['vehicle_company']) ? trim($_POST['vehicle_company']) : 0;
		$creator = isset($_POST['creator']) ? trim($_POST['creator']) : $this->sess->user_id;
		$creator_datetime = isset($_POST['creator_datetime']) ? trim($_POST['creator_datetime']) : date("Y-m-d H:i:s");
		$flag = isset($_POST['flag']) ? trim($_POST['flag']) : 0;
		
		$error = "";
		
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		
		if ($name == "")
		{
			$error .= "- Please fill Name \n";	
		}
		
		if ($code == "")
		{
			$error .= "- Please fill Code \n";	
		}else{
			
			$this->dbtransporter->where("distrep_code", $code);
			$this->dbtransporter->where("distrep_flag", 0);
			$this->dbtransporter->where("distrep_creator", $this->sess->user_id);
			$q = $this->dbtransporter->get("droppoint_distrep");
			
			if ($q->num_rows() > 0)
			{
				$row = $q->row();
				if ($row->distrep_id != $id)
				{
					$error .= "- Code already exist !! \n";
				}
			}
		}
		
		if ($parent == "")
		{
			$error .= "- Please Select Group \n";	
		}
		
		if ($error != "")
		{
			$callback['error'] = true;	
			$callback['message'] = $error;	
			
			echo json_encode($callback);			
			return;
		}
		
		unset($data);
		
		$data['distrep_name'] = $name;
		$data['distrep_code'] = $code;
		$data['distrep_vehicle_company'] = $vehicle_company;
		$data['distrep_company'] = $company;
		$data['distrep_parent'] = $parent;
		$data['distrep_creator'] = $creator;
		$data['distrep_creator_datetime'] = $creator_datetime;
		$data['distrep_flag'] = $flag;
		
		if ($id > 0)
		{
			$data['distrep_id'] = $id;
			$this->dbtransporter->where("distrep_id",$id);
			$this->dbtransporter->update("droppoint_distrep",$data);
			
			$callback['error'] = false;
			$callback['message'] = "Edit Data Success";
			echo json_encode($callback);
				
			return;
		}else{
			
			$this->dbtransporter->insert("droppoint_distrep", $data);
				
			$callback['error'] = false;
			$callback['message'] = "Insert Data Succes";
			echo json_encode($callback);
			
			return;
		}

	}
	
	function save_dataparent(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		
		$id = isset($_POST['id']) ? $_POST['id'] : 0;
		$name = isset($_POST['name']) ? trim($_POST['name']) : "";
		$code = isset($_POST['code']) ? trim($_POST['code']) : "";
		$company = isset($_POST['company']) ? trim($_POST['company']) : 0;
		$flag = isset($_POST['flag']) ? trim($_POST['flag']) : 0;
		$creator = isset($_POST['creator']) ? trim($_POST['creator']) : $this->sess->user_id;
		$creator_datetime = isset($_POST['creator_datetime']) ? trim($_POST['creator_datetime']) : date('Y-m-d H:i:s');
		
		
		$error = "";
		
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		
		if ($name == "")
		{
			$error .= "- Please fill Name ! \n";	
		}
		
		if ($code == "")
		{
			$error .= "- Please fill code ! \n";	
		}else{
			
			$this->dbtransporter->where("parent_code", $code);
			$this->dbtransporter->where("parent_flag", 0);
			$this->dbtransporter->where("parent_creator", $this->sess->user_id);
			$q = $this->dbtransporter->get("droppoint_parent");
			
			if ($q->num_rows() > 0)
			{
				$row = $q->row();
				if ($row->parent_id != $id)
				{
					$error .= "- Code already exist !! \n";
				}
			}
		}
		
		if ($company == 0)
		{
			$error .= "- Please Select Area ! \n";	
		}
		
		if ($creator == "")
		{
			$error .= "- Creator is Null  \n";	
		}
		
		if ($error != "")
		{
			$callback['error'] = true;	
			$callback['message'] = $error;	
			
			echo json_encode($callback);			
			return;
		}
		
		unset($data);
		
		$data['parent_name'] = $name;
		$data['parent_code'] = $code;
		$data['parent_company'] = $company;
		$data['parent_creator'] = $creator;
		$data['parent_creator_datetime'] = $creator_datetime;
		$data['parent_flag'] = $flag;
		
		if ($id > 0)
		{
			$data['parent_id'] = $id;
			$this->dbtransporter->where("parent_id",$id);
			$this->dbtransporter->update("droppoint_parent",$data);
			
			$callback['error'] = false;
			$callback['message'] = "Edit Data Success";
			echo json_encode($callback);
				
			return;
		}else{
			
			$this->dbtransporter->insert("droppoint_parent", $data);
				
			$callback['error'] = false;
			$callback['message'] = "Insert Data Succes";
			echo json_encode($callback);
			
			return;
		}

	}
	
	function delete_distrep($id){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$data["distrep_flag"] = 1;
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->where("distrep_id", $id);
		if($this->dbtransporter->update("droppoint_distrep", $data)){
		
			$callback['message'] = "Data has been deleted";
			$callback['error'] = false;	
		}else{
			$callback['message'] = "Failed delete data";
			$callback['error'] = true;	
		}
		echo json_encode($callback);
	}
	
	function delete_dataparent($id){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$data["parent_flag"] = 1;
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->where("parent_id", $id);
		if($this->dbtransporter->update("droppoint_parent", $data)){
		
			$callback['message'] = "Data has been deleted";
			$callback['error'] = false;	
		}else{
			$callback['message'] = "Failed delete data";
			$callback['error'] = true;	
		}
		echo json_encode($callback);
	}
	
	function delete($id){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$data["droppoint_flag"] = 1;
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->where("droppoint_id", $id);
		if($this->dbtransporter->update("droppoint", $data)){
		
			$callback['message'] = "Data has been deleted";
			$callback['error'] = false;	
		}else{
			$callback['message'] = "Failed delete data";
			$callback['error'] = true;	
		}
		echo json_encode($callback);
	}
	
	function get_parent_bycreator(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by("parent_name","asc");
		$this->dbtransporter->where("parent_flag", 0);
		$this->dbtransporter->where("parent_creator", $this->sess->user_id);
		$qd = $this->dbtransporter->get("droppoint_parent");
		$rd = $qd->result();
		return $rd;
	}
	
	function get_distrep_bycreator(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by("distrep_name","asc");
		$this->dbtransporter->where("distrep_flag", 0);
		$qd = $this->dbtransporter->get("droppoint_distrep");
		$rd = $qd->result();
		return $rd;
	}
	
	function get_geofence_bylogin(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		
		$this->db->select("geofence_id,geofence_user,geofence_name,geofence_type");
		$this->db->order_by("geofence_name","asc");
		$this->db->group_by("geofence_name");
		$this->db->where("geofence_status", 1);
		$this->db->where("geofence_user", $this->sess->user_id);
		$qd = $this->db->get("geofence");
		$rd = $qd->result();
		return $rd;
	}
	
	function get_company_bylogin(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$this->db->order_by("company_name","asc");
		$this->db->where("company_flag", 0);
		$this->db->where("company_created_by", $this->sess->user_id);
		$qd = $this->db->get("company");
		$rd = $qd->result();
		
		return $rd;
	}
	
}