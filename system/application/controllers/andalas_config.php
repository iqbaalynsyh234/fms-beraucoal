<?php
include "base.php";

class Andalas_config extends Base {

	function __construct()
	{
		parent::Base();
		if (! isset($this->sess->user_company)){
		redirect(base_url());
		}
	}

	function index(){
		
		if ($this->sess->user_type != 1){
			if (!$this->sess->user_company){
			redirect(base_url());
			}
		}
		
		$user_company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		$user_group = $this->sess->user_group;
		
		$this->dbtransporter = $this->load->database("transporter", TRUE);
				
		$this->dbtransporter->where("config_flag", 0);	
		$this->dbtransporter->where("config_company", $user_company);	
		$q = $this->dbtransporter->get("andalas_config");
		$rows = $q->result();
		
		$this->params["data"] = $rows;
		$this->params['sortby'] = "config_id";
		$this->params['orderby'] = "desc";
		$this->params['title'] = "Config List";
		
		$this->params["content"] =  $this->load->view('config/config_list', $this->params, true);	
		$this->load->view("templatesess", $this->params);	
	}
	
	function search(){
		
		if ($this->sess->user_type != 1){
			if (!$this->sess->user_company){
			redirect(base_url());
			}
		}
		
		$user_company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		$user_group = $this->sess->user_group;
		
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$status = isset($_POST['status']) ? $_POST['status'] : "";
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "config_id";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "desc";		
		
		$this->dbtransporter->order_by($sortby, $orderby);		
		$this->dbtransporter->where("config_flag", 0);	
		
		switch($field)
		{
			case "config_name":
				$this->dbtransporter->where("config_name LIKE '%".$keyword."%'", null);				
			break;			
			case "config_type":
				$this->dbtransporter->where("config_type LIKE '%".$keyword."%'", null);				
			break;
			
		}
		
		$this->dbtransporter->where("config_company", $user_company);	
		$q = $this->dbtransporter->get("andalas_config", $this->config->item("limit_records"), $offset);
		$rows = $q->result();
		
		$this->dbtransporter->select("count(*) as total");
		$this->dbtransporter->order_by($sortby, $orderby);
		$this->dbtransporter->where("config_flag", 0);
		switch($field)
		{
			case "config_name":
				$this->dbtransporter->where("config_name LIKE '%".$keyword."%'", null);				
			break;			
			case "config_address":
				$this->dbtransporter->where("config_address LIKE '%".$keyword."%'", null);				
			break;
				
		}
		
		$this->dbtransporter->where("config_company", $user_company);
		$qt = $this->dbtransporter->get("andalas_config");
		$rt = $qt->row();
		$total = $rt->total;
		
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
		

		$html = $this->load->view('config/config_list_result', $this->params, true);
		
		$callback['html'] = $html;
		$callback['total'] = $total;
				
				
		echo json_encode($callback);
	}
	
	function add()
	{
		if ($this->sess->user_type != 1){
			if (!$this->sess->user_company){
			redirect(base_url());
			}
		}
		
		$user_company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		$user_group = $this->sess->user_group;
		
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$id = $this->uri->segment(3);
		
		if ($id)
		{
			$this->dbtransporter->where("config_id", $id);
			$q = $this->dbtransporter->get("andalas_config");
			
			if ($q->num_rows() == 0)
			{
				redirect(base_url() . "andalas_config");
			}
			
			$row = $q->row();		
			
			$this->params['row'] = $row;
			$this->params['title'] = "Edit Config";
		}
		else
		{
			$this->params['title'] = "Add Config";
		}
		
		
		$this->params["content"] = $this->load->view('config/config_add', $this->params, true);		
		$this->load->view("templatesess", $this->params);	
	}
	
	function save()
	{
		if ($this->sess->user_type != 1){
			if (!$this->sess->user_company){
			redirect(base_url());
			}
		}
		
		$user_company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		$user_group = $this->sess->user_group;
		
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		
		$id = isset($_POST['config_id']) ? $_POST['config_id'] : 0;
		$name = isset($_POST['config_name']) ? trim($_POST['config_name']) : "";
		$type = isset($_POST['config_type']) ? trim($_POST['config_type']) : 0;
		$company = isset($_POST['config_company']) ? trim($_POST['config_company']) : 0;
		$status = isset($_POST['config_status']) ? trim($_POST['config_status']) : 0;
		$value_second = isset($_POST['config_second']) ? trim($_POST['config_second']) : 0;
		
		$error = "";
		
		if ($name == "")
		{
			$error .= "- Please fill name \n";	
		}
		/* if ($config_mobile == "")
		{
			$error .= "- Please fill mobile phone \n";	
		}else{
			if(!is_numeric($config_mobile)){
				$error .= "- Invalid Input, please input mobile phone with numeric \n";
			}
		} */
		if ($type == "")
		{
			$error .= "- Please select type \n";	
		}
		
		if ($company == "")
		{
			$error .= "- Company is null \n";	
		}
		
		if ($status == "")
		{
			$error .= "- Please select active status \n";	
		}
		
		if ($error != "")
		{
			$callback['error'] = true;	
			$callback['message'] = $error;	
			
			echo json_encode($callback);			
			return;
		}
		
		unset($data);
		$data['config_name'] = $name;
		$data['config_type'] = $type;
		$data['config_company'] = $company;
		$data['config_status'] = $status;
		
		if (isset($type)){
			if ($type == 1){
				$value_second = 3600;
				$data['config_second'] = $value_second;
			}
			if ($type == 2){
				$value_second = 3600*2;
				$data['config_second'] = $value_second;
			}
			
		}
		
		if ($id)
		{
			$this->dbtransporter->where("config_id", $id);
			$this->dbtransporter->update("andalas_config", $data);			
			
			$callback['error'] = false;
			$callback['message'] = "Config Successfully Updated";
			$callback['redirect'] = base_url()."andalas_config";
			
			echo json_encode($callback);
			
			return;
		}else{
			
			$this->dbtransporter->insert("andalas_config", $data);
			$id = $this->dbtransporter->insert_id();
		
			
			$callback['error'] = false;
			$callback['message'] = "Config Successfully Submitted";
			$callback['redirect'] = base_url()."andalas_config";
			
			echo json_encode($callback);
			
			return;
		}
	}
	
	function edit()
	{
		if ($this->sess->user_type != 1){
			if (!$this->sess->user_company){
			redirect(base_url());
			}
		}
		
		$user_company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		$user_group = $this->sess->user_group;
		
		$id = $this->uri->segment(3);
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		if ($id) {
		
			$this->dbtransporter->where("config_id", $id);
			$q = $this->dbtransporter->get("andalas_config");
			
			if ($q->num_rows == 0) { return; }
			$row = $q->row();
			$this->params['row'] = $row;
			$this->params['title'] = "Edit Config";
			$this->params['content'] = $this->load->view("config/config_edit", $this->params, true);
			$this->load->view("templatesess", $this->params);
			$this->dbtransporter->close();
		}
		else 
		{
			$this->dbtransporter->close();
			redirect(base_url()."andalas_config");
		}	
	}
	
	function update()
	{
		if ($this->sess->user_type != 1){
			if (!$this->sess->user_company){
			redirect(base_url());
			}
		}
		
		$user_company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		$user_group = $this->sess->user_group;
		
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		
		$id = isset($_POST['config_id']) ? $_POST['config_id'] : 0;
		$name = isset($_POST['config_name']) ? trim($_POST['config_name']) : "";
		$type = isset($_POST['config_type']) ? trim($_POST['config_type']) : 0;
		$company = isset($_POST['config_company']) ? trim($_POST['config_company']) : 0;
		$status = isset($_POST['config_status']) ? trim($_POST['config_status']) : 0;
		$value_second = isset($_POST['config_second']) ? trim($_POST['config_second']) : 0;
		
		$error = "";
		
		if ($name == "")
		{
			$error .= "- Please fill name \n";	
		}
		/* if ($config_mobile == "")
		{
			$error .= "- Please fill mobile phone \n";	
		}else{
			if(!is_numeric($config_mobile)){
				$error .= "- Invalid Input, please input mobile phone with numeric \n";
			}
		} */
		if ($type == "")
		{
			$error .= "- Please select type \n";	
		}
		if ($company == "")
		{
			$error .= "- Company is null \n";	
		}
		/* if ($status == "")
		{
			$error .= "- Please select active status \n";	
		} */
		
		if ($error != "")
		{
			$callback['error'] = true;	
			$callback['message'] = $error;	
			
			echo json_encode($callback);			
			return;
		}
		
		if ($error != "")
		{
			$callback['error'] = true;	
			$callback['message'] = $error;	
			
			echo json_encode($callback);			
			return;
		}
		
		$data['config_name'] = $name;
		$data['config_type'] = $type;
		$data['config_company'] = $company;
		$data['config_status'] = $status;
		
		if (isset($type)){
			if ($type == 1){
				$value_second = 3600;
				$data['config_second'] = $value_second;
			}
			if ($type == 2){
				$value_second = 3600*2;
				$data['config_second'] = $value_second;
			}
			
		}
		
		$this->dbtransporter->where("config_id", $id);
		$this->dbtransporter->update("andalas_config", $data);
		$this->dbtransporter->close();		

		$callback['error'] = false;
		$callback['message'] = "Config Successfully Updated";
		$callback['redirect'] = base_url()."andalas_config";
			
		echo json_encode($callback);
			
		return;
			
	}
	
	function delete($id)
	{
		if ($this->sess->user_type != 1){
			if (!$this->sess->user_company){
			redirect(base_url());
			}
		}
		
		$user_company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		$user_group = $this->sess->user_group;
		
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		
		$data["config_flag"] = 1;		
		$this->dbtransporter->where("config_id", $id);
		if($this->dbtransporter->update("andalas_config", $data)){
			$callback['message'] = "Data has been deleted";
			$callback['error'] = false;	
		}else{
			$callback['message'] = "Failed delete data";
			$callback['error'] = true;	
		}
		
		echo json_encode($callback);
	}

}