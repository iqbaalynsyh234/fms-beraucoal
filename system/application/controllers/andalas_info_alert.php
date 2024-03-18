<?php
include "base.php";

class Andalas_info_alert extends Base {

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
		
		$this->params['sortby'] = "alert_dispatcher_id";
		$this->params['orderby'] = "asc";
		$this->params['title'] = "Info Alert List";
		
		
		$this->params["content"] =  $this->load->view('info_alert/info_alert_list', $this->params, true);	
		$this->load->view("templatesess", $this->params);	
	}
	
	function search_info_alert(){
		
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
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "alert_dispatcher_id";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "asc";		
		
		$this->dbtransporter->order_by($sortby, $orderby);		
		$this->dbtransporter->where("alert_dispatcher_flag", 0);	
		
		switch($field)
		{
			case "alert_dispatcher_name":
				$this->dbtransporter->where("alert_dispatcher_name LIKE '%".$keyword."%'", null);				
			break;			
			case "alert_dispatcher_mobile":
				$this->dbtransporter->where("alert_dispatcher_mobile LIKE '%".$keyword."%'", null);				
			break;
			case "alert_dispatcher_email":
				$this->dbtransporter->where("alert_dispatcher_email LIKE '%".$keyword."%'", null);				
			break;
			
		}
		
		$this->dbtransporter->where("alert_dispatcher_company", $user_company);	
		$q = $this->dbtransporter->get("andalas_info_alert", $this->config->item("limit_records"), $offset);
		$rows = $q->result();
		
		$this->dbtransporter->select("count(*) as total");
		$this->dbtransporter->order_by($sortby, $orderby);
		$this->dbtransporter->where("alert_dispatcher_flag", 0);
		switch($field)
		{
			case "alert_dispatcher_name":
				$this->dbtransporter->where("alert_dispatcher_name LIKE '%".$keyword."%'", null);				
			break;			
			case "alert_dispatcher_mobile":
				$this->dbtransporter->where("alert_dispatcher_mobile LIKE '%".$keyword."%'", null);				
			break;
			case "alert_dispatcher_email":
				$this->dbtransporter->where("alert_dispatcher_email LIKE '%".$keyword."%'", null);				
			break;
				
		}
		
		$this->dbtransporter->where("alert_dispatcher_company", $user_company);
		$qt = $this->dbtransporter->get("andalas_info_alert");
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
		

		$html = $this->load->view('info_alert/info_alert_list_result', $this->params, true);
		
		$callback['html'] = $html;
		$callback['total'] = $total;
				
				
		echo json_encode($callback);
	}
	
	function add_info_alert()
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
			$this->dbtransporter->where("alert_dispatcher_id", $id);
			$q = $this->dbtransporter->get("info_alert");
			
			if ($q->num_rows() == 0)
			{
				redirect(base_url() . "info_alert");
			}
			
			$row = $q->row();		
			
			$this->params['row'] = $row;
			//$this->params['runit'] = $rc;			
			$this->params['title'] = "Edit Info Alert";
		}
		else
		{
			//$this->params['runit'] = $rc;
			$this->params['title'] = "Add Info Alert";
		}
		
		
		$this->params["content"] = $this->load->view('info_alert/info_alert_add', $this->params, true);		
		$this->load->view("templatesess", $this->params);	
	}
	
	function info_alert_save()
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
		$id = isset($_POST['alert_dispatcher_id']) ? $_POST['alert_dispatcher_id'] : 0;
		
		$alert_dispatcher_name = isset($_POST['alert_dispatcher_name']) ? trim($_POST['alert_dispatcher_name']) : "";
		$alert_dispatcher_mobile = isset($_POST['alert_dispatcher_mobile']) ? trim($_POST['alert_dispatcher_mobile']) : "";
		$alert_dispatcher_email = isset($_POST['alert_dispatcher_email']) ? trim($_POST['alert_dispatcher_email']) : "";
		$alert_dispatcher_company = isset($_POST['alert_dispatcher_company']) ? trim($_POST['alert_dispatcher_company']) : 0;
		$alert_dispatcher_info_mobile = isset($_POST['alert_dispatcher_info_mobile']) ? trim($_POST['alert_dispatcher_info_mobile']) : 0;
		$alert_dispatcher_info_email = isset($_POST['alert_dispatcher_info_email']) ? trim($_POST['alert_dispatcher_info_email']) : 0;
		$alert_dispatcher_config_mobile = isset($_POST['alert_dispatcher_config_mobile']) ? trim($_POST['alert_dispatcher_config_mobile']) : 0;
		$alert_dispatcher_config_email = isset($_POST['alert_dispatcher_config_email']) ? trim($_POST['alert_dispatcher_config_email']) : 0;
		$alert_dispatcher_status = isset($_POST['alert_dispatcher_status']) ? trim($_POST['alert_dispatcher_status']) : 1;
		
		$error = "";
		
		if ($alert_dispatcher_name == "")
		{
			$error .= "- Please fill name \n";	
		}
		if ($alert_dispatcher_mobile == "")
		{
			$error .= "- Please fill mobile phone \n";	
		}else{
			if(!is_numeric($alert_dispatcher_mobile)){
				$error .= "- Invalid Input, please input mobile phone with numeric \n";
			}
		}
		if ($alert_dispatcher_email == "")
		{
			$error .= "- Please fill email \n";	
		}
		if ($alert_dispatcher_config_mobile == "")
		{
			$error .= "- Please select alert by mobile phone \n";	
		}
		if ($alert_dispatcher_config_email == "")
		{
			$error .= "- Please select alert by email \n";	
		}
		if ($alert_dispatcher_status == "")
		{
			$error .= "- Please select status \n";
		}
		if ($error != "")
		{
			$callback['error'] = true;	
			$callback['message'] = $error;	
			
			echo json_encode($callback);			
			return;
		}
		
		unset($data);
		$data['alert_dispatcher_name'] = $alert_dispatcher_name;
		$data['alert_dispatcher_mobile'] = $alert_dispatcher_mobile;
		$data['alert_dispatcher_email'] = $alert_dispatcher_email;
		$data['alert_dispatcher_company'] = $alert_dispatcher_company;
		$data['alert_dispatcher_config_email'] = $alert_dispatcher_config_email;
		$data['alert_dispatcher_config_mobile'] = $alert_dispatcher_config_mobile;
		$data['alert_dispatcher_status'] = $alert_dispatcher_status;

		if ($id)
		{
			$this->dbtransporter->where("alert_dispatcher_id", $id);
			$this->dbtransporter->update("andalas_info_alert", $data);			
			
			$callback['error'] = false;
			$callback['message'] = "Dispatcher Successfully Updated";
			$callback['redirect'] = base_url()."andalas_info_alert";
			
			echo json_encode($callback);
			
			return;
		}else{
			
			$this->dbtransporter->insert("andalas_info_alert", $data);
			$id = $this->dbtransporter->insert_id();
		
			
			$callback['error'] = false;
			$callback['message'] = "Dispatcher Successfully Submitted";
			$callback['redirect'] = base_url()."andalas_info_alert";
			
			echo json_encode($callback);
			
			return;
		}
	}
	
	function edit_info_alert()
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
		
			$this->dbtransporter->where("alert_dispatcher_id", $id);
			$q = $this->dbtransporter->get("andalas_info_alert");
			
			if ($q->num_rows == 0) { return; }
			$row = $q->row();
			$this->params['row'] = $row;
			$this->params['title'] = "Edit Data Dispatcher";
			$this->params['content'] = $this->load->view("info_alert/info_alert_edit", $this->params, true);
			$this->load->view("templatesess", $this->params);
			$this->dbtransporter->close();
		}
		else 
		{
			$this->dbtransporter->close();
			redirect(base_url()."andalas_info_alert");
		}	
	}
	
	function update_info_alert()
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
		$id = isset($_POST['alert_dispatcher_id']) ? trim($_POST['alert_dispatcher_id']) : "";
		
		$alert_dispatcher_name = isset($_POST['alert_dispatcher_name']) ? trim($_POST['alert_dispatcher_name']) : "";
		$alert_dispatcher_mobile = isset($_POST['alert_dispatcher_mobile']) ? trim($_POST['alert_dispatcher_mobile']) : "";
		$alert_dispatcher_email = isset($_POST['alert_dispatcher_email']) ? trim($_POST['alert_dispatcher_email']) : "";
		$alert_dispatcher_company = isset($_POST['alert_dispatcher_company']) ? trim($_POST['alert_dispatcher_company']) : 0;
		$alert_dispatcher_info_mobile = isset($_POST['alert_dispatcher_info_mobile']) ? trim($_POST['alert_dispatcher_info_mobile']) : 0;
		$alert_dispatcher_info_email = isset($_POST['alert_dispatcher_info_email']) ? trim($_POST['alert_dispatcher_info_email']) : 0;
		$alert_dispatcher_config_mobile = isset($_POST['alert_dispatcher_config_mobile']) ? trim($_POST['alert_dispatcher_config_mobile']) : 0;
		$alert_dispatcher_config_email = isset($_POST['alert_dispatcher_config_email']) ? trim($_POST['alert_dispatcher_config_email']) : 0;
		$alert_dispatcher_status = isset($_POST['alert_dispatcher_status']) ? trim($_POST['alert_dispatcher_status']) : 0;
		
		unset($data);
		$error = "";
		
		if ($alert_dispatcher_name == "")
		{
			$error .= "- Please fill name \n";	
		}
		if ($alert_dispatcher_mobile == "")
		{
			$error .= "- Please fill mobile phone \n";	
		}else{
			if(!is_numeric($alert_dispatcher_mobile)){
				$error .= "- Invalid Input, please input mobile phone with numeric \n";
			}
		}
		if ($alert_dispatcher_email == "")
		{
			$error .= "- Please fill email \n";	
		}
		if ($alert_dispatcher_config_mobile == "")
		{
			$error .= "- Please select alert by mobile phone \n";	
		}
		if ($alert_dispatcher_config_email == "")
		{
			$error .= "- Please select alert by email \n";	
		}
		/* if ($alert_dispatcher_status == "")
		{
			$error .= "- Please select status \n";
		} */
		if ($error != "")
		{
			$callback['error'] = true;	
			$callback['message'] = $error;	
			
			echo json_encode($callback);			
			return;
		}
		
		$data['alert_dispatcher_name'] = $alert_dispatcher_name;
		$data['alert_dispatcher_mobile'] = $alert_dispatcher_mobile;
		$data['alert_dispatcher_email'] = $alert_dispatcher_email;
		$data['alert_dispatcher_company'] = $alert_dispatcher_company;
		$data['alert_dispatcher_config_email'] = $alert_dispatcher_config_email;
		$data['alert_dispatcher_config_mobile'] = $alert_dispatcher_config_mobile;
		$data['alert_dispatcher_status'] = $alert_dispatcher_status;
		
		$this->dbtransporter->where("alert_dispatcher_id", $id);
		$this->dbtransporter->update("andalas_info_alert", $data);
		$this->dbtransporter->close();		

		$callback['error'] = false;
		$callback['message'] = "Dispatcher Successfully Updated";
		$callback['redirect'] = base_url()."andalas_info_alert";
			
		echo json_encode($callback);
			
		return;
			
	}
	
	function delete_info_alert($id)
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
		
		$data["alert_dispatcher_flag"] = 1;		
		$this->dbtransporter->where("alert_dispatcher_id", $id);
		if($this->dbtransporter->update("andalas_info_alert", $data)){
			$callback['message'] = "Data has been deleted";
			$callback['error'] = false;	
		}else{
			$callback['message'] = "Failed delete data";
			$callback['error'] = true;	
		}
		
		echo json_encode($callback);
	}

}