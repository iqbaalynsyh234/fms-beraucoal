<?php
include "base.php";

class Andalas_customer_company extends Base {

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
		
		$this->params['sortby'] = "customer_company_id";
		$this->params['orderby'] = "desc";
		$this->params['title'] = "Customer Company List";
		
		$this->params["content"] =  $this->load->view('customer_company/customer_company_list', $this->params, true);	
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
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "customer_company_id";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "desc";		
		
		$this->dbtransporter->order_by($sortby, $orderby);		
		$this->dbtransporter->where("customer_company_flag", 0);	
		
		switch($field)
		{
			case "customer_company_name":
				$this->dbtransporter->where("customer_company_name LIKE '%".$keyword."%'", null);				
			break;			
			case "customer_company_address":
				$this->dbtransporter->where("customer_company_address LIKE '%".$keyword."%'", null);				
			break;
			
		}
		
		$this->dbtransporter->where("customer_company_usercompany", $user_company);	
		$q = $this->dbtransporter->get("andalas_customer_company", $this->config->item("limit_records"), $offset);
		$rows = $q->result();
		
		$this->dbtransporter->select("count(*) as total");
		$this->dbtransporter->order_by($sortby, $orderby);
		$this->dbtransporter->where("customer_company_flag", 0);
		switch($field)
		{
			case "customer_company_name":
				$this->dbtransporter->where("customer_company_name LIKE '%".$keyword."%'", null);				
			break;			
			case "customer_company_address":
				$this->dbtransporter->where("customer_company_address LIKE '%".$keyword."%'", null);				
			break;
				
		}
		
		$this->dbtransporter->where("customer_company_usercompany", $user_company);
		$qt = $this->dbtransporter->get("andalas_customer_company");
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
		

		$html = $this->load->view('customer_company/customer_company_list_result', $this->params, true);
		
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
			$this->dbtransporter->where("customer_company_id", $id);
			$q = $this->dbtransporter->get("andalas_customer_company");
			
			if ($q->num_rows() == 0)
			{
				redirect(base_url() . "andalas_customer_company");
			}
			
			$row = $q->row();		
			
			$this->params['row'] = $row;
			$this->params['title'] = "Edit Customer Company";
		}
		else
		{
			$this->params['title'] = "Add Customer Company";
		}
		
		
		$this->params["content"] = $this->load->view('customer_company/customer_company_add', $this->params, true);		
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
		
		$id = isset($_POST['customer_company_id']) ? $_POST['customer_company_id'] : 0;
		$name = isset($_POST['customer_company_name']) ? trim($_POST['customer_company_name']) : "";
		$cust_company = isset($_POST['customer_company_usercompany']) ? trim($_POST['customer_company_usercompany']) : 0;
		$address = isset($_POST['customer_company_address']) ? trim($_POST['customer_company_address']) : "";
		$status = isset($_POST['customer_company_status']) ? trim($_POST['customer_company_status']) : 0;
		
		$error = "";
		
		if ($name == "")
		{
			$error .= "- Please fill name \n";	
		}
		/* if ($customer_company_mobile == "")
		{
			$error .= "- Please fill mobile phone \n";	
		}else{
			if(!is_numeric($customer_company_mobile)){
				$error .= "- Invalid Input, please input mobile phone with numeric \n";
			}
		} */
		if ($address == "")
		{
			$error .= "- Please fill address \n";	
		}
		
		
		if ($error != "")
		{
			$callback['error'] = true;	
			$callback['message'] = $error;	
			
			echo json_encode($callback);			
			return;
		}
		
		unset($data);
		$data['customer_company_name'] = $name;
		$data['customer_company_address'] = $address;
		$data['customer_company_usercompany'] = $cust_company;
		$data['customer_company_status'] = $status;

		if ($id)
		{
			$this->dbtransporter->where("customer_company_id", $id);
			$this->dbtransporter->update("andalas_customer_company", $data);			
			
			$callback['error'] = false;
			$callback['message'] = "Company Successfully Updated";
			$callback['redirect'] = base_url()."andalas_customer_company";
			
			echo json_encode($callback);
			
			return;
		}else{
			
			$this->dbtransporter->insert("andalas_customer_company", $data);
			$id = $this->dbtransporter->insert_id();
		
			
			$callback['error'] = false;
			$callback['message'] = "Company Successfully Submitted";
			$callback['redirect'] = base_url()."andalas_customer_company";
			
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
		
			$this->dbtransporter->where("customer_company_id", $id);
			$q = $this->dbtransporter->get("andalas_customer_company");
			
			if ($q->num_rows == 0) { return; }
			$row = $q->row();
			$this->params['row'] = $row;
			$this->params['title'] = "Edit Data Company";
			$this->params['content'] = $this->load->view("customer_company/customer_company_edit", $this->params, true);
			$this->load->view("templatesess", $this->params);
			$this->dbtransporter->close();
		}
		else 
		{
			$this->dbtransporter->close();
			redirect(base_url()."andalas_customer_company");
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
		
		$id = isset($_POST['customer_company_id']) ? trim($_POST['customer_company_id']) : "";
		$name = isset($_POST['customer_company_name']) ? trim($_POST['customer_company_name']) : "";
		$cust_company = isset($_POST['customer_company_usercompany']) ? trim($_POST['customer_company_usercompany']) : 0;
		$address = isset($_POST['customer_company_address']) ? trim($_POST['customer_company_address']) : "";
		$status = isset($_POST['customer_company_status']) ? trim($_POST['customer_company_status']) : 0;
		$error = "";
		
		if ($name == "")
		{
			$error .= "- Please fill name \n";	
		}
		/* if ($customer_company_mobile == "")
		{
			$error .= "- Please fill mobile phone \n";	
		}else{
			if(!is_numeric($customer_company_mobile)){
				$error .= "- Invalid Input, please input mobile phone with numeric \n";
			}
		} */
		if ($address == "")
		{
			$error .= "- Please fill address \n";	
		}
		
		if ($error != "")
		{
			$callback['error'] = true;	
			$callback['message'] = $error;	
			
			echo json_encode($callback);			
			return;
		}
		
		$data['customer_company_name'] = $name;
		$data['customer_company_address'] = $address;
		$data['customer_company_usercompany'] = $cust_company;
		$data['customer_company_status'] = $status;
		
		$this->dbtransporter->where("customer_company_id", $id);
		$this->dbtransporter->update("andalas_customer_company", $data);
		$this->dbtransporter->close();		

		$callback['error'] = false;
		$callback['message'] = "Company Successfully Updated";
		$callback['redirect'] = base_url()."andalas_customer_company";
			
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
		
		$data["customer_company_flag"] = 1;		
		$this->dbtransporter->where("customer_company_id", $id);
		if($this->dbtransporter->update("andalas_customer_company", $data)){
			$callback['message'] = "Data has been deleted";
			$callback['error'] = false;	
		}else{
			$callback['message'] = "Failed delete data";
			$callback['error'] = true;	
		}
		
		echo json_encode($callback);
	}

}