<?php
include "base.php";

class Andalas_customer extends Base {

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
		
		$this->params['sortby'] = "customer_id";
		$this->params['orderby'] = "desc";
		$this->params['title'] = "Customer List";
		
		$this->params["content"] =  $this->load->view('customer/customer_list', $this->params, true);	
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
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "customer_id";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "desc";		
		
		$this->dbtransporter->order_by($sortby, $orderby);		
		$this->dbtransporter->where("customer_flag", 0);	
		
		switch($field)
		{
			case "customer_name":
				$this->dbtransporter->where("customer_name LIKE '%".$keyword."%'", null);				
			break;			
			case "customer_email":
				$this->dbtransporter->where("customer_email LIKE '%".$keyword."%'", null);				
			break;
			case "customer_phone":
				$this->dbtransporter->where("customer_phone LIKE '%".$keyword."%'", null);				
			break;
			case "customer_mobile":
				$this->dbtransporter->where("customer_mobile LIKE '%".$keyword."%'", null);				
			break;
			case "customer_address":
				$this->dbtransporter->where("customer_address LIKE '%".$keyword."%'", null);				
			break;
			
		}
		$this->dbtransporter->join("andalas_customer_company", "customer_company_group = customer_company_id", "left");
		$this->dbtransporter->where("customer_company", $user_company);	
		$q = $this->dbtransporter->get("andalas_customer", $this->config->item("limit_records"), $offset);
		$rows = $q->result();
		
		$this->dbtransporter->select("count(*) as total");
		$this->dbtransporter->order_by($sortby, $orderby);
		$this->dbtransporter->where("customer_flag", 0);
		switch($field)
		{
			case "customer_name":
				$this->dbtransporter->where("customer_name LIKE '%".$keyword."%'", null);				
			break;			
			case "customer_email":
				$this->dbtransporter->where("customer_email LIKE '%".$keyword."%'", null);				
			break;
			case "customer_phone":
				$this->dbtransporter->where("customer_phone LIKE '%".$keyword."%'", null);				
			break;
			case "customer_mobile":
				$this->dbtransporter->where("customer_mobile LIKE '%".$keyword."%'", null);				
			break;
			case "customer_address":
				$this->dbtransporter->where("customer_address LIKE '%".$keyword."%'", null);				
			break;
				
		}
		$this->dbtransporter->join("andalas_customer_company", "customer_company_group = customer_company_id", "left");
		$this->dbtransporter->where("customer_company", $user_company);
		$qt = $this->dbtransporter->get("andalas_customer");
		$rt = $qt->row();
		$total = $rt->total;
		
		$this->load->library("pagination1");
		
		$config['uri_segment'] = 3;
		$config['total_rows'] = $total;
		$config['per_page'] = $this->config->item("limit_records");
		
		$this->pagination1->initialize($config);
		
		//Get customer company
		/* $row_customer_company = $this->get_customer_company();
		$this->params["ccompany"] = $row_customer_company; */
		
		$this->params["paging"] = $this->pagination1->create_links();
		$this->params["offset"] = $offset;
		$this->params["total"] = $total;
		$this->params["data"] = $rows;
		$this->params['sortby'] = $sortby;
		$this->params['orderby'] = $orderby;
		

		$html = $this->load->view('customer/customer_list_result', $this->params, true);
		
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
		//Get customer company
		$row_customer_company = $this->get_customer_company();
		$this->params['rccompany'] = $row_customer_company;
		//print_r($row_customer_company);exit();
		if ($id)
		{
			$this->dbtransporter->where("customer_id", $id);
			$q = $this->dbtransporter->get("andalas_customer");
			
			if ($q->num_rows() == 0)
			{
				redirect(base_url() . "andalas_customer");
			}
			
			$row = $q->row();		
			
			$this->params['row'] = $row;
			$this->params['title'] = "Edit Data Customer";
		}
		else
		{
			$this->params['title'] = "Add Data Customer";
		}
		
		$this->params["content"] = $this->load->view('customer/customer_add', $this->params, true);		
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
		
		$id = isset($_POST['customer_id']) ? $_POST['customer_id'] : 0;
		$name = isset($_POST['customer_name']) ? trim($_POST['customer_name']) : "";
		$company = isset($_POST['customer_company']) ? trim($_POST['customer_company']) : 0;
		$email = isset($_POST['customer_email']) ? trim($_POST['customer_email']) : "";
		$phone = isset($_POST['customer_phone']) ? trim($_POST['customer_phone']) : 0;
		$mobile = isset($_POST['customer_mobile']) ? trim($_POST['customer_mobile']) : 0;
		$address = isset($_POST['customer_address']) ? trim($_POST['customer_address']) : "";
		$status = isset($_POST['customer_status']) ? trim($_POST['customer_status']) : 0;
		$sex = isset($_POST['customer_sex']) ? trim($_POST['customer_sex']) : "";
		$note = isset($_POST['customer_note']) ? trim($_POST['customer_note']) : "";
		$company_group = isset($_POST['customer_company_group']) ? trim($_POST['customer_company_group']) : 0;
		$alert_email = isset($_POST['customer_alert_email']) ? trim($_POST['customer_alert_email']) : 0;
		$alert_sms = isset($_POST['customer_alert_sms']) ? trim($_POST['customer_alert_sms']) : 0;
		$time = isset($_POST['customer_update_time']) ? trim($_POST['customer_update_time']) : date("Y-m-d H:i:s");
		
		
		$error = "";
		
		if ($name == "")
		{
			$error .= "- Please fill name \n";	
		}
		if ($phone == "")
		{
			$error .= "- Please fill phone \n";	
		}else{
			if(!is_numeric($phone)){
				$error .= "- Invalid Input, please input phone with numeric \n";
			}
		}
		if ($mobile == "")
		{
			$error .= "- Please fill mobile phone \n";	
		}else{
			if(!is_numeric($mobile)){
				$error .= "- Invalid Input, please input mobile phone with numeric \n";
			}
		}
		/* if ($address == "")
		{
			$error .= "- Please fill address \n";	
		} */
		if ($email == "")
		{
			$error .= "- Please fill address \n";	
		}
		if ($company == "")
		{
			$error .= "- Company ID is Null \n";	
		}
		if ($company_group == "")
		{
			$error .= "- Please select company \n";	
		}
		if ($status == "")
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
		$data['customer_name'] = $name;
		$data['customer_company'] = $company;
		$data['customer_email'] = $email;
		$data['customer_phone'] = $phone;
		$data['customer_mobile'] = $mobile;
		$data['customer_address'] = $address;
		$data['customer_status'] = $status;
		$data['customer_sex'] = $sex;
		$data['customer_note'] = $note;
		$data['customer_company_group'] = $company_group;
		$data['customer_alert_email'] = $alert_email;
		$data['customer_alert_sms'] = $alert_sms;
		$data['customer_update_time'] = $time;
		
		if ($id)
		{
			$this->dbtransporter->where("customer_id", $id);
			$this->dbtransporter->update("andalas_customer", $data);			
			
			$callback['error'] = false;
			$callback['message'] = "Customer Successfully Updated";
			$callback['redirect'] = base_url()."andalas_customer";
			
			echo json_encode($callback);
			
			return;
		}else{
			
			$this->dbtransporter->insert("andalas_customer", $data);
			$id = $this->dbtransporter->insert_id();
		
			
			$callback['error'] = false;
			$callback['message'] = "Customer Successfully Submitted";
			$callback['redirect'] = base_url()."andalas_customer";
			
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
		//Get customer company
		$row_customer_company = $this->get_customer_company();
		$this->params['rccompany'] = $row_customer_company;
		
		if ($id) {
		
			$this->dbtransporter->where("customer_id", $id);
			$q = $this->dbtransporter->get("andalas_customer");
			
			if ($q->num_rows == 0) { return; }
			$row = $q->row();
			$this->params['row'] = $row;
			$this->params['title'] = "Edit Data Company";
			$this->params['content'] = $this->load->view("customer/customer_edit", $this->params, true);
			$this->load->view("templatesess", $this->params);
			$this->dbtransporter->close();
		}
		else 
		{
			$this->dbtransporter->close();
			redirect(base_url()."andalas_customer");
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
		
		$id = isset($_POST['customer_id']) ? $_POST['customer_id'] : 0;
		$name = isset($_POST['customer_name']) ? trim($_POST['customer_name']) : "";
		$company = isset($_POST['customer_company']) ? trim($_POST['customer_company']) : 0;
		$email = isset($_POST['customer_email']) ? trim($_POST['customer_email']) : "";
		$phone = isset($_POST['customer_phone']) ? trim($_POST['customer_phone']) : 0;
		$mobile = isset($_POST['customer_mobile']) ? trim($_POST['customer_mobile']) : 0;
		$address = isset($_POST['customer_address']) ? trim($_POST['customer_address']) : "";
		$status = isset($_POST['customer_status']) ? trim($_POST['customer_status']) : 0;
		$sex = isset($_POST['customer_sex']) ? trim($_POST['customer_sex']) : "";
		$note = isset($_POST['customer_note']) ? trim($_POST['customer_note']) : "";
		$company_group = isset($_POST['customer_company_group']) ? trim($_POST['customer_company_group']) : 0;
		$alert_email = isset($_POST['customer_alert_email']) ? trim($_POST['customer_alert_email']) : 0;
		$alert_sms = isset($_POST['customer_alert_sms']) ? trim($_POST['customer_alert_sms']) : 0;
		$time = isset($_POST['customer_update_time']) ? trim($_POST['customer_update_time']) : date("Y-m-d H:i:s");
		
		$error = "";
		
		if ($name == "")
		{
			$error .= "- Please fill name \n";	
		}
		if ($phone == "")
		{
			$error .= "- Please fill phone \n";	
		}else{
			if(!is_numeric($phone)){
				$error .= "- Invalid Input, please input phone with numeric \n";
			}
		}
		if ($mobile == "")
		{
			$error .= "- Please fill mobile phone \n";	
		}else{
			if(!is_numeric($mobile)){
				$error .= "- Invalid Input, please input mobile phone with numeric \n";
			}
		}
		/* if ($address == "")
		{
			$error .= "- Please fill address \n";	
		} */
		if ($email == "")
		{
			$error .= "- Please fill address \n";	
		}
		if ($company == "")
		{
			$error .= "- Company ID is Null \n";	
		}
		if ($company_group == "")
		{
			$error .= "- Please select company \n";	
		}
		/* if ($status == "")
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
		
		unset($data);
		$data['customer_name'] = $name;
		$data['customer_company'] = $company;
		$data['customer_email'] = $email;
		$data['customer_phone'] = $phone;
		$data['customer_mobile'] = $mobile;
		$data['customer_address'] = $address;
		$data['customer_status'] = $status;
		$data['customer_sex'] = $sex;
		$data['customer_note'] = $note;
		$data['customer_company_group'] = $company_group;
		$data['customer_alert_email'] = $alert_email;
		$data['customer_alert_sms'] = $alert_sms;
		$data['customer_update_time'] = $time;
		
		$this->dbtransporter->where("customer_id", $id);
		$this->dbtransporter->update("andalas_customer", $data);
		$this->dbtransporter->close();		

		$callback['error'] = false;
		$callback['message'] = "Customer Successfully Updated";
		$callback['redirect'] = base_url()."andalas_customer";
			
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
		
		$data["customer_flag"] = 1;		
		$this->dbtransporter->where("customer_id", $id);
		if($this->dbtransporter->update("andalas_customer", $data)){
			$callback['message'] = "Data has been deleted";
			$callback['error'] = false;	
		}else{
			$callback['message'] = "Failed delete data";
			$callback['error'] = true;	
		}
		
		echo json_encode($callback);
	}
	
	function get_customer_company()
	{
		if ($this->sess->user_type != 1){
			if (!$this->sess->user_company){
			redirect(base_url());
			}
		}
		
		$user_company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		$user_group = $this->sess->user_group;
		
		$this->dbtransporter=$this->load->database("transporter", TRUE);
		
		$this->dbtransporter->order_by('customer_company_name','asc');
		$this->dbtransporter->where('customer_company_status',1);
		$this->dbtransporter->where('customer_company_flag',0);
		if ($type_admin != 1){
			$this->dbtransporter->where('customer_company_usercompany',$this->sess->user_company);
		}
		$q_customer_company = $this->dbtransporter->get('andalas_customer_company');
		$row_customer_company = $q_customer_company->result();
        
		$data_customer_company = $row_customer_company;
		
        $this->dbtransporter->cache_delete_all();
        return $data_customer_company;
	}

}