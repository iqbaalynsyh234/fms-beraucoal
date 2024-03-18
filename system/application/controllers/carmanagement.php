<?php
include "base.php";
class Carmanagement extends Base {
	function Carmanagement()
	{
			parent::Base();	
			$this->load->model("gpsmodel");

			if (! isset($this->sess->user_company)){
				redirect(base_url());
			}
			
			$this->load->helper('common_helper');
	}
	
	function settenant()
	{
        $customer_company = $this->sess->user_company;
		
		if (!$customer_company){
		redirect(base_url());
		}
		
		$this->dbcar=$this->load->database("rentcar", TRUE);
		
		$rows_customer = $this->get_customer();
		$this->params["customer"] = $rows_customer;
		
		$id = $this->input->post("id");
		$this->db->where("vehicle_id", $id);
		$qm = $this->db->get("vehicle");
		
		$row = $qm->row();
		$params['row'] = $row;
        
        $this->dbcar->order_by("customer_name");
		$this->dbcar->where("customer_company", $customer_company);
		$this->dbcar->where("customer_status", 1);
		$this->dbcar->where("customer_flag", 0);
		
		$qc = $this->dbcar->get("customer");
        $rc = $qc->result();
		
		//$params['start_date'] = $enddate;
		//$params['end_date'] = $startdate;
		
		$params['rcustomer'] = $rc;
		$callback['html'] = $this->load->view("carmanagement/add_request", $params, true);
        $callback['error'] = false;	
        echo json_encode($callback);
		

    }
	
	function savetenant()
	{
		$settenant_company = $this->sess->user_company;
		
		if (!$settenant_company){
		redirect(base_url());
		}
		
		$this->dbcar=$this->load->database("rentcar", TRUE);
		
		$settenant_company = isset($_POST['settenant_company']) ? $_POST['settenant_company'] : "";
		$settenant_id = isset($_POST['settenant_id']) ? $_POST['settenant_id'] : "";
		$settenant_name = isset($_POST['settenant_name']) ? $_POST['settenant_name'] : "";
		$start_date = isset($_POST['start_date']) ? $_POST['start_date'] : "";
		$end_date = isset($_POST['end_date']) ? $_POST['end_date'] : "";
        $expired_date = isset($_POST['expired_date']) ? $_POST['expired_date'] : $end_date;
        $longtime = isset($_POST['longtime']) ? $_POST['longtime'] : $longtime;
		$vehicle_no = isset($_POST['vehicle_no']) ? $_POST['vehicle_no'] : "";
		$vehicle_name = isset($_POST['vehicle_name']) ? $_POST['vehicle_name'] : "";
		$vehicle_status = isset($_POST['vehicle_status']) ? $_POST['vehicle_status'] : "0";
		$settenant_flag = isset($_POST['settenant_flag']) ? $_POST['settenant_flag'] : "0";
		
		$getdiff = strtotime($end_date) -  strtotime($start_date);
		$longtime = $getdiff/(60*60*24);
			//60 detik * 60 menit * 24 jam = 1 hari
			//echo $row-longtime;
					
		$error = "";
        if ($settenant_name == "")
		{
			$error .= "- Please Select Tenant \n";	
		}
		
		if ($start_date == "")
		{
			$error .= "- Please Select Start date \n";	
		}
        
        if ($end_date == "")
		{
			$error .= "- Please Select End Date \n";	
		}
		
        if ($end_date < $start_date)
		{
			$error .= "- Please Check Your End Date \n";	
		}
                
		if ($error != "")
		{
			$callback['error'] = true;	
			$callback['message'] = $error;	
			
			echo json_encode($callback);			
			return;
		}
		
		unset($data);
			
			$data['settenant_company'] = $settenant_company;
			$data['settenant_id'] = $settenant_id;
            $data['settenant_name'] = $settenant_name;
            $data['longtime'] = $longtime;
            $data['start_date'] = $start_date;
            $data['end_date'] = $end_date;
			$data['expired_date'] = $end_date;
			$data['vehicle_no'] = $vehicle_no;
            $data['vehicle_name'] = $vehicle_name;
            $data['vehicle_status'] = $vehicle_status;
            $data['settenant_flag'] = $settenant_flag;
            
            $this->dbcar->insert("settenant_vehicle", $data);
		
			$callback['error'] = false;	
			$callback['message'] = "Add Rental Successfully Submitted";
			$callback["redirect"]= base_url()."trackers";
			echo json_encode($callback);
			return;
	}
	
	function thecar()
	{
		
		
		$this->dbcar=$this->load->database("rentcar", TRUE);
		
		$this->dbcar->select("*");
        $this->dbcar->from("rentcar_car_name");
        $q = $this->dbcar->get();
        $data_thecar = $q->result();
        $this->dbcar->cache_delete_all();
        return $data_thecar;
	}
	
	function get_customer()
	{
		$customer_company = $this->sess->user_company;
		
		if (!$customer_company){
		redirect(base_url());
		}
		
		$this->dbcar=$this->load->database("rentcar", TRUE);
		
		$this->dbcar->select("*");
		$this->dbcar->from("rentcar_customer");
        $this->dbcar->where("customer_company", $customer_company);
        $q = $this->dbcar->get();
        $data_customer = $q->result();
        $this->dbcar->cache_delete_all();
        return $data_customer;
	}
	
	function getdata_customer()
	{
		$customer_company = $this->sess->user_company;
		
		if (!$customer_company){
		redirect(base_url());
		}
		
		$this->dbcar=$this->load->database("rentcar",TRUE);
			
		$id	= isset($_POST["customer_id"]) ? $_POST["customer_id"]: "";
		
		$this->dbcar->select("*");
		$this->dbcar->from("rentcar_customer");
		$this->dbcar->where("customer_id", $id);
		$d	= $this->dbcar->get();
		$cs = $d->result();
		$this->dbcar->cache_delete_all();
		return $cs;
		
		$callback["error"]=false;
		$callback["message"]="Data Lengkap";
		$callback["redirect"]= base_url()."carmanagement/request_car";
		
		echo json_encode($callback);
		return;
	}
	
	//TENANT
	
	function data_tenant()
	{
		$customer_company = $this->sess->user_company;
		
		if (!$customer_company){
		redirect(base_url());
		}
		
		$this->dbcar = $this->load->database('rentcar', true);
		$offset = isset($_POST['offset']) ? $_POST['offset'] : "";
        $field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
        $keyword = $this->input->post("keyword");
		$customerstatus = $this->input->post("customer_status");
		
		if (!$this->sess->user_type == 1)
		{
		  redirect(base_url());
		}
		
		switch($field)
		{
			case "customer_name":
				$this->dbcar->where("customer_name LIKE '%".$keyword."%'", null);
			break;
			
			case "customer_mobile":
				$this->dbcar->where("customer_mobile LIKE '%".$keyword."%'", null);
			break;
			
			case "customer_phone":
				$this->dbcar->where("customer_phone LIKE '%".$keyword."%'", null);
			break;
			
			case "customer_email":
				$this->dbcar->where("customer_email LIKE '%".$keyword."%'", null);
			break;
			
			case "customer_address":
				$this->dbcar->where("customer_address LIKE '%".$keyword."%'", null);
			break;
			
			case "customer_status":
				$this->dbcar->where("customer_status", $customerstatus);
			break;
		}
		
		$this->dbcar->select("*");
		$this->dbcar->from("customer");
		$this->dbcar->where("customer_company", $customer_company);
		$this->dbcar->where("customer_flag", 0);
		$this->dbcar->orderby("customer_name","asc");
		$q = $this->dbcar->get("", $this->config->item("limit_record"), $offset);
		$rows = $q->result();
		$total = count($rows);
		$config['uri_segment'] = 4;
		$config['base_url'] = base_url()."carmanagement/data_tenant/".$field."/".$keyword;
		$config['total_rows'] = $total;
		$config['per_page'] = $this->config->item("limit_records");
		
		$this->pagination->initialize($config);
		
		$this->params["title"] = "Tenant Manage";
		$this->params["field"] = $field;
		$this->params["keyword"] = $keyword;
		$this->params["paging"] = $this->pagination->create_links();
		$this->params["offset"] = $offset;
		$this->params["total"] = $total;
		$this->params["data"] = $rows;
		$this->params["content"] = $this->load->view("carmanagement/data_tenant", $this->params, true);
		$this->load->view("templatesess", $this->params);
		
		$this->dbcar->close();
	}
	
	function add_tenant ()
	{
        $customer_company = $this->sess->user_company;
		
		if (!$customer_company){
		redirect(base_url());
		}
		
        $this->params["content"] = $this->load->view('carmanagement/add_tenant', $this->params, true);
        $this->load->view("templatesess", $this->params);
    }
	
	function savenew ()
	{
		$this->dbcar=$this->load->database("rentcar", TRUE);	
		
		$customer_company = $this->sess->user_company;
		
		if (!$customer_company){
		redirect(base_url());
		}
		
		$customer_name			= isset($_POST["customer_name"]) ? $_POST["customer_name"]: "";
		$customer_address		= isset($_POST["customer_address"]) ? $_POST["customer_address"]: "";
		$customer_phone			= isset($_POST["customer_phone"]) ? $_POST["customer_phone"]: "";	
		$customer_mobile		= isset($_POST["customer_mobile"]) ? $_POST["customer_mobile"]: "";
		$customer_email			= isset($_POST["customer_email"]) ? $_POST["customer_email"]: "";
		$customer_idcard		= isset($_POST["customer_idcard"]) ? $_POST["customer_idcard"]: "";
		$customer_status		= isset($_POST["customer_status"]) ? $_POST["customer_status"]: 1;
 		$customer_keterangan	= isset($_POST["customer_keterangan"]) ? $_POST["customer_keterangan"]: "";
		$customer_company		= isset($_POST["customer_company"]) ? $_POST["customer_company"]: "";
		$customer_flag			= isset($_POST["customer_flag"]) ? $_POST["customer_flag"]: "0";
		
		$error = "";
		
		if (!$customer_name)
		{
			echo json_encode(array("error"=>true, "message"=>"Please Input Tenant Name"));
			return;
		}
		
		if	(!$customer_address)
		{
			echo json_encode(array("error"=>true, "message"=>"Please Input Tenant Address"));
			return;
		}
		
		if (!$customer_phone)
		{
			echo json_encode(array("error"=>true, "message"=>"Please Input Tenant Phone Number"));
			return;
		}
		
		if (!$customer_mobile)
		{
			echo json_encode(array("error"=>true, "message"=>"Please Input Tenant Mobile Number"));
			return;
		}
		
		if (!$customer_email)
		{
			echo json_encode(array("error"=>true, "message"=>"Please Input Tenant Email"));
			return;
		}
		
		if (!$customer_idcard)
		{
			echo json_encode(array("error"=>true, "messsage"=>"Please Input Tenant Idcard or Pasport"));
			return;
		}
		
		if ($customer_status == "" || $customer_status == "") 
		{
			echo json_encode(array("error"=>true, "message"=>"Please Select Status Penyewa"));
			return;
		}
		
		unset ($data);
		
		$data["customer_name"]			= $customer_name;
		$data["customer_address"]		= $customer_address;
		$data["customer_phone"]			= $customer_phone;
		$data["customer_mobile"]		= $customer_mobile;
		$data["customer_email"]			= $customer_email;
		$data["customer_idcard"]		= $customer_idcard;
		$data["customer_status"]		= $customer_status;
		$data["customer_keterangan"]	= $customer_keterangan;
		$data["customer_company"]		= $customer_company;
		$data["customer_flag"]			= $customer_flag;
		
		
		
		$this->dbcar->insert("customer",$data);
		$this->dbcar->cache_delete_all();
		
		$callback["error"]=false;
		$callback["message"]="Add Tenant Successfully Submitted";
		$callback["redirect"]= base_url()."carmanagement/data_tenant";
		
		echo json_encode($callback);
		return;
		
	}
	
	function edit()
	{
		$this->dbcar=$this->load->database("rentcar", TRUE);	
		
	    $customer_company = $this->sess->user_company;
		
		if (!$customer_company){
		redirect(base_url());
		}
		
		$customer_id = $this->uri->segment(3);
		if ($customer_id) 
		{
		$this->dbcar = $this->load->database("rentcar", true);
		$this->dbcar->select("*");
		$this->dbcar->from("customer");
		$this->dbcar->where("customer_id", $customer_id);
		$q = $this->dbcar->get();
		if ($q->num_rows == 0) { return; }
			$row = $q->row();
			$this->params['row'] = $row;
			$this->params['title'] = "Edit Tenant";
			$this->params['content'] = $this->load->view("carmanagement/edit_tenant", $this->params, true);
			$this->load->view("templatesess", $this->params);
			$this->dbcar->close();
		} 
		else 
		{
			$this->dbcar->close();
			redirect(base_url()."carmanagement/data_tenant");
		}
	}
	
	function update()
	{
		$this->dbcar = $this->load->database('rentcar', true);
		$customer_id			= isset($_POST["customer_id"]) ? $_POST["customer_id"]: "";
		$customer_name			= isset($_POST["customer_name"]) ? $_POST["customer_name"]: "";
		$customer_address		= isset($_POST["customer_address"]) ? $_POST["customer_address"]: "";
		$customer_phone			= isset($_POST["customer_phone"]) ? $_POST["customer_phone"]: "";	
		$customer_mobile		= isset($_POST["customer_mobile"]) ? $_POST["customer_mobile"]: "";
		$customer_email			= isset($_POST["customer_email"]) ? $_POST["customer_email"]: "";
		$customer_idcard		= isset($_POST["customer_idcard"]) ? $_POST["customer_idcard"]: "";
		$customer_status		= isset($_POST["customer_status"]) ? $_POST["customer_status"]: "";
		$customer_keterangan	= isset($_POST["customer_keterangan"]) ? $_POST["customer_keterangan"]: "";
		$customer_company		= isset($_POST["customer_company"]) ? $_POST["customer_company"]: "";
		$customer_flag			= isset($_POST["customer_flag"]) ? $_POST["customer_flag"]: "";
		
		
		if (!$customer_name)
		{
			echo json_encode(array("error"=>true, "message"=>"Please Input Tenant Name"));
			return;
		}
		
		if	(!$customer_address)
		{
			echo json_encode(array("error"=>true, "message"=>"Please Input Tenant Address"));
			return;
		}
		
		if (!$customer_phone)
		{
			echo json_encode(array("error"=>true, "message"=>"Please Input Tenant Phone Number"));
			return;
		}
		
		if (!$customer_mobile)
		{
			echo json_encode(array("error"=>true, "message"=>"Please Input Tenant Mobile Number"));
			return;
		}
		
		if (!$customer_email)
		{
			echo json_encode(array("error"=>true, "message"=>"Please Input Tenant Email"));
			return;
		}
		
		if (!$customer_idcard)
		{
			echo json_encode(array("error"=>true, "messsage"=>"Please Input Tenant Idcard or Pasport"));
			return;
		}
		
		if ($customer_status == "" || $customer_status == "") 
		{
			echo json_encode(array("error"=>true, "message"=>"Please Select Status Penyewa"));
			return;
		}
		
		unset ($data);
		
		$data["customer_name"]			= $customer_name;
		$data["customer_address"]		= $customer_address;
		$data["customer_phone"]			= $customer_phone;
		$data["customer_mobile"]		= $customer_mobile;
		$data["customer_email"]			= $customer_email;
		$data["customer_idcard"]		= $customer_idcard;
		$data["customer_status"]		= $customer_status;
		$data["customer_keterangan"]	= $customer_keterangan;
		$data["customer_company"]		= $customer_company;
		$data["customer_flag"]			= $customer_flag;
		
		$this->dbcar->where('customer_id', $customer_id);
		$this->dbcar->update('customer', $data);
		$this->dbcar->close();
		
		redirect (base_url()."carmanagement/data_tenant", 'refresh');
	}
	
	function detail_tenant()
	{
		$customer_company = $this->sess->user_company;
		
		if (!$customer_company){
		redirect(base_url());
		}
		
		$id = $this->input->post("id");
		$this->dbcar = $this->load->database("rentcar", true);		
		$this->dbcar->where("customer_id", $id);
		$q = $this->dbcar->get("customer");
		
		if ($q->num_rows() == 0)
		{
			$callback['error'] = true;	
			echo json_encode($callback);
			return;
		}
		
		$row = $q->row();
		
		//select tenant image
		$this->dbcar->select("*");
		$this->dbcar->from("customer_image");
		$this->dbcar->where("customer_image_customer_id", $id);
		$q = $this->dbcar->get();
		$row_image = $q->row();

		$this->dbcar->close();
		
		$params['row_image'] = $row_image;
		$params['row'] = $row;
		$html = $this->load->view("carmanagement/detail_tenant", $params, true);		
		
		$callback['html'] = $html;
		$callback['error'] = false;	
		
		echo json_encode($callback);
	}
	
	function detail_status_tenant()
	{	
		$customer_company = $this->sess->user_company;
		
		if (!$customer_company){
		redirect(base_url());
		}
		
		$id = $this->input->post("id");
		$this->dbcar = $this->load->database("rentcar", true);		
		$this->dbcar->where("customer_id", $id);
		$q = $this->dbcar->get("customer");
		
		if ($q->num_rows() == 0)
		{
			$callback['error'] = true;	
			echo json_encode($callback);
			return;
		}
		
		$row = $q->row();
		
		//select tenant image
		$this->dbcar->select("*");
		$this->dbcar->from("customer_image");
		$this->dbcar->where("customer_image_customer_id", $id);
		$q = $this->dbcar->get();
		$row_image = $q->row();

		$this->dbcar->close();
		
		$params['row_image'] = $row_image;
		$params['row'] = $row;
		$html = $this->load->view("carmanagement/detail_tenant_status", $params, true);		
		
		$callback['html'] = $html;
		$callback['error'] = false;	
		
		echo json_encode($callback);
	}
	
	//blacklist
	
	function tenant_blacklist()
	{
		/*$customer_company = $this->sess->user_company;
		
		if (!$customer_company){
		redirect(base_url());
		}*/
		
		$this->dbcar = $this->load->database('rentcar', true);
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
        $field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
        $keyword = $this->input->post("keyword");
		
		
		switch($field)
		{
			case "customer_name":
				$this->dbcar->where("customer_name LIKE '%".$keyword."%'", null);
			break;
			
			case "customer_mobile":
				$this->dbcar->where("customer_mobile LIKE '%".$keyword."%'", null);
			break;
			
			case "customer_phone":
				$this->dbcar->where("customer_phone LIKE '%".$keyword."%'", null);
			break;
			
			case "customer_email":
				$this->dbcar->where("customer_email LIKE '%".$keyword."%'", null);
			break;
			
			case "customer_address":
				$this->dbcar->where("customer_address LIKE '%".$keyword."%'", null);
			break;
			
			case "customer_keterangan":
				$this->dbcar->where("customer_keterangan LIKE '%".$keyword."%'", null);
			break;
			
		}
		
		$black = "BlackList";
		$this->dbcar->select("*");
		$this->dbcar->from("customer");
		$this->dbcar->where("customer_status", $black);
		$this->dbcar->where("customer_flag", 0);
		$this->dbcar->orderby("customer_name","asc");
		$q = $this->dbcar->get("", $this->config->item("limit_record"), $offset);
		$rows = $q->result();
		$total = count($rows);
		$config['uri_segment'] = 4;
		$config['base_url'] = base_url()."carmanagement/blacklist/".$field."/".$keyword;
		$config['total_rows'] = $total;
		$config['per_page'] = $this->config->item("limit_records");
		
		$this->pagination->initialize($config);
		
		$this->params["title"] = "Tenant Blacklist";
		$this->params["field"] = $field;
		$this->params["keyword"] = $keyword;
		$this->params["paging"] = $this->pagination->create_links();
		$this->params["offset"] = $offset;
		$this->params["total"] = $total;
		$this->params["data"] = $rows;
		$this->params["content"] = $this->load->view("carmanagement/blacklist", $this->params, true);
		$this->load->view("templatesess", $this->params);
		
		$this->dbcar->close();
	}
	
	//status vehicle
	function status_vehicle()
	{	
		$this->dbcar = $this->load->database('rentcar', true);
		$settenant_company = $this->sess->user_company;
		
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
        $field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
        $keyword = $this->input->post("keyword");
		$vehiclestatus = $this->input->post("vehicle_status");
		$customer = $this->input->post("settenant_name");
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "customer_name";
        $orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "asc";
        $startdate = isset($_POST['startdate']) ? $_POST['startdate'] : "";
		$enddate = isset($_POST['enddate']) ? $_POST['enddate'] : "";
		$searchdate = isset($_POST['searchdate']) ? $_POST['searchdate'] : "all";
		
		if (!$settenant_company){
		redirect(base_url());
		}
		
		$this->dbcar->join("rentcar_customer", "customer_id = settenant_name", "left");
        $this->dbcar->order_by("customer_name");
		$q = $this->dbcar->get("rentcar_settenant_vehicle", $this->config->item("limit_records"), $offset);
        $rows = $q->result();
		
		if($searchdate != "all"){
				if($startdate != ""){
						$this->dbcar->where("start_date >=", $startdate);
					}
					if($enddate != ""){
						$this->dbcar->where("start_date <=", $enddate);
					}
			}
		
		switch($field){
		case "vehicle_no":
			$this->dbcar->where("vehicle_no LIKE '%".$keyword."%'", null);
		break;
        case "vehicle_name":
            $this->dbcar->where("vehicle_name LIKE '%".$keyword."%'", null);				
		break;
		case "longtime":
            $this->dbcar->where("longtime LIKE '%".$keyword."%'", null);				
		break;	
		case "vehicle_status":
			$this->dbcar->where("vehicle_status", $vehiclestatus);				
		break;
		case "data_is_expired":
				$this->dbcar->where("expired_date <", date("Y-m-d"));
		break;
		case "data_will_expired":
			$this->dbcar->where("expired_date >=", date("Y-m-d"));
			$end_expired = date("Y-m-d", strtotime("+3 days"));
			$this->dbcar->where("expired_date <=", $end_expired);
		break;
		
		}
		
		
        $this->dbcar->select("*");
		$this->dbcar->from("rentcar_settenant_vehicle");
		$this->dbcar->where("settenant_company", $settenant_company);
		$this->dbcar->where("settenant_flag", 0);
		$this->dbcar->order_by("settenant_id", "desc");
		$q = $this->dbcar->get("", $this->config->item("limit_records"), $offset);
        $rows = $q->result();
		
		if($searchdate != "all"){
				if($startdate != ""){
						$this->dbcar->where("start_date >=", $startdate);
					}
					if($enddate != ""){
						$this->dbcar->where("start_date <=", $enddate);
					}
			}
			
		switch($field){
		case "vehicle_no":
			$this->dbcar->where("vehicle_no LIKE '%".$keyword."%'", null);
		break;
        case "vehicle_name":
            $this->dbcar->where("vehicle_name LIKE '%".$keyword."%'", null);				
		break;
		case "longtime":
            $this->dbcar->where("longtime LIKE '%".$keyword."%'", null);				
		break;	
		case "vehicle_status":
			$this->dbcar->where("vehicle_status", $vehiclestatus);				
		break;
		case "data_is_expired":
				$this->dbcar->where("expired_date <", date("Y-m-d"));
		break;
		case "data_will_expired":
			$this->dbcar->where("expired_date >=", date("Y-m-d"));
			$end_expired = date("Y-m-d", strtotime("+3 days"));
			$this->dbcar->where("expired_date <=", $end_expired);
		break;
		
        }
		
		
		
		
		$this->dbcar->join("rentcar_customer", "customer_id = settenant_name", "left");
        $qr = $this->dbcar->get("rentcar_settenant_vehicle");
        $rc = $qr->row();
		
		$total = count($rows);
		
		$config['uri_segment'] = 5;
		$config['base_url'] = base_url()."carmanagement/status_vehicle/".$keyword;
		$config['total_rows'] = $total;
		$config['per_page'] = $this->config->item("limit_records");
		
		$this->pagination->initialize($config);
		
		$customer = $this->get_customer();
		$this->params["settenant_name"] = $customer;
		$this->params["title"] = "Status Vehicle";
		//$this->params["field"] = $field;
		$this->params["keyword"] = $keyword;
		$this->params["paging"] = $this->pagination->create_links();
		$this->params["offset"] = $offset;
		$this->params["total"] = $total;
		$this->params["data"] = $rows;
		
		$this->params['sortby'] = "customer_name";
        $this->params['orderby'] = "asc";
		$this->dbcar->order_by("customer_name");
		$q = $this->dbcar->get("rentcar_customer");
		$rows = $q->result();
		$this->params['customer'] = $rows;
		$this->params['vehicle_status'] = $rows;
		$this->params["content"] = $this->load->view("carmanagement/status_vehicle", $this->params, true);
		$this->load->view("templatesess", $this->params);
		
		$this->dbcar->close();
	}
	
	function edit_status()
	{
		$customer_company = $this->sess->user_company;
		
		if (!$customer_company){
		redirect(base_url());
		}
		
		$rows_customer = $this->get_customer();
		$this->params["customer"] = $rows_customer;
		
		$settenant_id = $this->uri->segment(3);
		
		$this->dbcar = $this->load->database("rentcar", true);
        $this->dbcar->order_by("customer_name");
		$this->dbcar->where("customer_company", $customer_company);
		$this->dbcar->where("customer_status", 1);
		$qc = $this->dbcar->get("customer");
        $rc = $qc->result();
		
		if ($settenant_id) {
		    $this->dbcar->where("settenant_id", $settenant_id);
            $this->dbcar->join("rentcar_customer", "customer_id = settenant_name", "left");
            $q = $this->dbcar->get("rentcar_settenant_vehicle");
			
		
		if ($q->num_rows == 0) { return; }
			$row = $q->row();
			$this->params['row'] = $row;
			$this->params['rcustomer'] = $rc;
			$this->params['title'] = "Edit Status Vehicle";
			$this->params['content'] = $this->load->view("carmanagement/edit_status", $this->params, true);
			$this->load->view("templatesess", $this->params);
			$this->dbcar->close();
		} 
		else 
		{
			$this->dbcar->close();
			redirect(base_url()."carmanagement/status_vehicle");
		}
	}
	
	function updatestatus()
	{
		
		$this->dbcar = $this->load->database('rentcar', true);
		$settenant_id		= isset($_POST["settenant_id"]) ? $_POST["settenant_id"]: "";
		$settenant_name		= isset($_POST["settenant_name"]) ? $_POST["settenant_name"]: "";
		$vehicle_name		= isset($_POST["vehicle_name"]) ? $_POST["vehicle_name"]: "";
		$vehicle_no			= isset($_POST["vehicle_no"]) ? $_POST["vehicle_no"]: "";	
		$longtime			= isset($_POST["longtime"]) ? $_POST["longtime"]: $longtime;
		$start_date			= isset($_POST["start_date"]) ? $_POST["start_date"]: "";
		$end_date			= isset($_POST["end_date"]) ? $_POST["end_date"]: "";
		$expired_date		= isset($_POST["expired_date"]) ? $_POST["expired_date"]: "";
		$vehicle_status		= isset($_POST["vehicle_status"]) ? $_POST["vehicle_status"]: "";
		$settenant_company	= isset($_POST["settenant_company"]) ? $_POST["settenant_company"]: "";
		$settenant_flag		= isset($_POST["settenant_flag"]) ? $_POST["settenant_flag"]: "0";
		
		$getdiff = strtotime($end_date) -  strtotime($start_date);
		$longtime = ($getdiff/(60*60*24));
			//60 detik * 60 menit * 24 jam = 1 hari
			//echo $row-longtime;
		
		$error = "";
        if ($settenant_name == "")
		{
			$error .= "- Please Select Tenant \n";	
		}
		
		if ($start_date == "")
		{
			$error .= "- Please Select Start date \n";	
		}
        
        if ($end_date == "")
		{
			$error .= "- Please Select End Date \n";	
		}
		
        if ($end_date < $start_date)
		{
			$error .= "- Please Check Your End Date \n";	
		}
                
		if ($error != "")
		{
			$callback['error'] = true;	
			$callback['message'] = $error;	
			
			echo json_encode($callback);			
			return;
		}
		
		unset ($data);
		
		$data["settenant_name"]		= $settenant_name;
		$data["vehicle_name"]		= $vehicle_name;
		$data["vehicle_no"]			= $vehicle_no;
		$data["longtime"]			= $longtime;
		$data["start_date"]			= $start_date;
		$data["end_date"]			= $end_date;
		$data["expired_date"]		= $expired_date;
		$data["vehicle_status"]		= $vehicle_status;
		$data["settenant_company"]	= $settenant_company;
		$data["settenant_flag"]		= $settenant_flag;
		
		$this->dbcar->where('settenant_id', $settenant_id);
		$this->dbcar->update('settenant_vehicle', $data);
		$this->dbcar->close();
		redirect (base_url()."carmanagement/status_vehicle", 'refresh');
		
		/*$callback['error'] = false;
		$callback['message'] = "Status Vehicle Successfully Updated";
		$callback['redirect'] = base_url()."carmanagement/status_vehicle";
			
		echo json_encode($callback);
		return;*/

	}
	
	function report_to_excel()
	{
		
		$settenant_name = $this->input->post("settenant_name");
		$vehicle_name = $this->input->post("vehicle_name");
		$vehicle_no = $this->input->post("vehicle_no");
		$longtime = $this->input->post("longtime");
		$start_date = $this->input->post("start_date");
		$end_date = $this->input->post("end_date");
		$expired_date = $this->input->post("expired_date");
		$vehicle_status = $this->input->post("vehicle_status");
		
		$sdate = date("d-m-Y", strtotime($start_date . " "));
		$edate = date("d-m-Y", strtotime($end_date . " " ));
		
		//print_r($sdate." ".$edate);
		//print_r($vehicle.$startdate.$enddate.$shour.$ehour.$driver);
		
		$this->dbcar = $this->load->database("rentcar", true);
		$this->dbcar->where("settenant_company", $this->sess->user_company);
		$this->dbcar->order_by("settenant_id", "desc");
		$this->dbcar->where("start_date >=", $sdate);
		$this->dbcar->where("start_date <=", $edate);
		
		if ($vehicle_name != 0)
		{
			$this->dbcar->where("vehicle_name", $vehicle_name);
		}
		if ($vehicle_no != 0)
		{
			$this->dbcar->where("vehicle_no", $vehicle_no);
		}
		
		$this->dbcar->order_by("settenant_id","desc");
		$this->dbcar->order_by("start_date","desc");
		$this->dbcar->order_by("vehicle_no","asc");
		$this->dbcar->order_by("vehicle_name","asc");
		$q = $this->dbcar->get("settenant_vehicle");
		$rows = $q->result();
		$tenant = $this->get_customer();
		
		/** PHPExcel */
			include 'class/PHPExcel.php';
			include 'class/PHPExcel/Writer/Excel2007.php';
			$objPHPExcel = new PHPExcel();
			
			// Set properties
			$objPHPExcel->getProperties()->setCreator("lacak-mobil.com");
			$objPHPExcel->getProperties()->setLastModifiedBy("rentcar.lacak-mobil.com");
			$objPHPExcel->getProperties()->setTitle("Status Vehicle");
			$objPHPExcel->getProperties()->setSubject("Status Vehicle");
			$objPHPExcel->getProperties()->setDescription("Status Vehicle");
			
			//set document
			$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(1);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.50);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.50);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(1);
			$objPHPExcel->getDefaultStyle()->getFont()->setName('Times New Roman');
			$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
			$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
			$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(16);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);			
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(12);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
			$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
			
			//Header
			$objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->mergeCells('A1:I1');
			$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Vehicle Report');
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->SetCellValue('B3', 'Date :');
			$objPHPExcel->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
			$filter_date = strtotime($sdate ." ") . " ~ " . strtotime($edate ." ");
			$objPHPExcel->getActiveSheet()->SetCellValue('C3', $filter_date);
			
			//Top Header
			$objPHPExcel->getActiveSheet()->SetCellValue('B5', 'No');
			$objPHPExcel->getActiveSheet()->SetCellValue('C5', 'Tenant Name');
			$objPHPExcel->getActiveSheet()->SetCellValue('D5', 'Vehicle');
			$objPHPExcel->getActiveSheet()->SetCellValue('E5', 'Longtime');
			$objPHPExcel->getActiveSheet()->SetCellValue('F5', 'Start Date');
			$objPHPExcel->getActiveSheet()->SetCellValue('G5', 'End Date');
			$objPHPExcel->getActiveSheet()->SetCellValue('H5', 'Expired Date');
			$objPHPExcel->getActiveSheet()->SetCellValue('I5', 'Status');
			$objPHPExcel->getActiveSheet()->getStyle('A5:J5')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A5:J5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		for ($i=0;$i<count($rows);$i++)
		{
			$objPHPExcel->getActiveSheet()->getStyle('A'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->SetCellValue('B'.(5+$i), $i+1);
			$objPHPExcel->getActiveSheet()->SetCellValue('C'.(5+$i), $rows[$i]->vehicle_name." ".$rows[$i]->vehicle_no);
			
			if (isset($tenants))
					{
						foreach($tenants as $tenant)
						{
							if ($tenant->customer_id == $data[$i]->settenant_name)
							{
								$objPHPExcel->getActiveSheet()->SetCellValue('D'.(5+$i), $tenant->customer_name);
							}
						}
					}
			$objPHPExcel->getActiveSheet()->SetCellValue('E'.(5+$i), $rows[$i]->longtime);
			$objPHPExcel->getActiveSheet()->SetCellValue('F'.(5+$i), $rows[$i]->start_date);
			$objPHPExcel->getActiveSheet()->getStyle('F'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->SetCellValue('G'.(5+$i), $rows[$i]->end_date);
			$objPHPExcel->getActiveSheet()->getStyle('G'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->SetCellValue('H'.(5+$i), $rows[$i]->expired_date);
			$objPHPExcel->getActiveSheet()->getStyle('H'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			//$objPHPExcel->getActiveSheet()->SetCellValue('I'.(5+$i), ($row[$i]->vehicle_status == 1) ? $this->config->item('Complete') : $this->config->item('Rent') );
			/*if ($row[$i]->vehicle_status == 1){
				$objPHPExcel->getActiveSheet()->SetCellValue('I'.(5+$i), $row[$i]->vehicle_status = "Complete");
			}
			
			if ($row[$i]->vehicle_status == 0){
				$objPHPExcel->getActiveSheet()->SetCellValue('I'.(5+$i), $row[$i]->vehicle_status = "Rent");
			*/
		}
		
		$styleArray = array(
				  'borders' => array(
				    'allborders' => array(
				      'style' => PHPExcel_Style_Border::BORDER_THIN
				    )
				  )
				);
			$objPHPExcel->getActiveSheet()->getStyle('A5:I'.(4+$i))->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getStyle('A5:I'.(4+$i))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
			$objPHPExcel->getActiveSheet()->getStyle('A5:I'.(4+$i))->getAlignment()->setWrapText(true);
			// Rename sheet
			$objPHPExcel->getActiveSheet()->setTitle('Status Vehicle');
			
					
			// Save Excel
			$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
			@mkdir(REPORT_PATH, DIR_WRITE_MODE); //REPORT_PATH -> config->constant
			$filecreatedname = "Status_Vehicle_Report_".date('YmdHis') . ".xls";
			
			$objWriter->save(REPORT_PATH.$filecreatedname);
		
			$output = '{"success":true,"errMsg":"","filename":"' .base_url() . "assets/media/report/" . $filecreatedname.'"}';
			echo $output;
	}
	
	//photo
	function upload_image()
	{
		if (! isset($this->sess->user_company)) {
			redirect(base_url());
		}
		
		$tenant_id = $this->input->post("id");
		$this->load->helper(array('form'));

		$this->dbcar = $this->load->database("rentcar", true);
		$this->dbcar->select("*");
		$this->dbcar->from("customer");
		$this->dbcar->where("customer_id", $tenant_id);
		$q = $this->dbcar->get();
		$row = $q->row();
		
		//select tenant image
		$this->dbcar->select("*");
		$this->dbcar->from("customer_image");
		$this->dbcar->where("customer_image_customer_id", $tenant_id);
		$q = $this->dbcar->get();
		$row_image = $q->row();

		$this->dbcar->close();
		
		$params['row_image'] = $row_image;
		$params['row'] = $row;
		$params["title"] = "Manage Tenant- Upload Images";		
		$params["customer_id"] = $tenant_id;
		$params["error_upload"] = "";
		$html = $this->load->view("carmanagement/upload_image", $params, true);
		$callback['error'] = false;
		$callback['html'] = $html;		
		echo json_encode($callback);
		
		//$this->load->view("templatesess", $this->params);
		
	}
	
	function save_image() 
	{
	
		$config['upload_path'] = './assets/rentcar/images/photo/';
		$config['allowed_types'] = 'gif|jpeg|jpg|png';
		$config['max_size'] = '100';
		$config['max_width'] = '1024';
		$config['max_height'] = '768';
		
		$this->load->library('upload', $config);
		$tenant_image_tenant_id = $this->input->post("customer_id");
		
		if (!$this->upload->do_upload()) {
			
			$error = array('error' => $this->upload->display_errors());
			//print_r($error);exit();
			//$this->load->view('carmanagement/upload_image', $error);
			//$this->load->view('carmanagement/upload_error', $error);
			redirect(base_url()."carmanagement/data_tenant");
		} 
		
		else {
			
			$this->dbcar = $this->load->database("rentcar", true);
			$data = array('upload_data' => $this->upload->data());
			
			//print_r($data);exit();
			
			$tenant_image_file_name = $data['upload_data']['file_name'];
			$tenant_image_file_type = $data['upload_data']['file_type'];
			$tenant_image_file_path = $data['upload_data']['file_path'];
			$tenant_image_full_path = $data['upload_data']['full_path'];
			$tenant_image_raw_name = $data['upload_data']['raw_name'];
			$tenant_image_orig_name = $data['upload_data']['orig_name'];
			$tenant_image_client_name = $data['upload_data']['client_name'];
			$tenant_image_file_ext = $data['upload_data']['file_ext'];
			$tenant_image_file_size = $data['upload_data']['file_size'];
			$tenant_image_is_image = $data['upload_data']['is_image'];
			$tenant_image_image_width = $data['upload_data']['image_width'];
			$tenant_image_image_height = $data['upload_data']['image_height'];
			$tenant_image_image_type = $data['upload_data']['image_type'];
			$tenant_image_image_size_str = $data['upload_data']['image_size_str'];
			
			unset($data_insert);
				$data_insert['customer_image_customer_id'] = $tenant_image_tenant_id;
				$data_insert['customer_image_file_name'] = $tenant_image_file_name;
				$data_insert['customer_image_file_path'] = $tenant_image_file_path;
				$data_insert['customer_image_full_path'] = $tenant_image_full_path;
				$data_insert['customer_image_raw_name'] = $tenant_image_raw_name;
				$data_insert['customer_image_orig_name'] = $tenant_image_orig_name;
				$data_insert['customer_image_client_name'] = $tenant_image_client_name;
				$data_insert['customer_image_file_ext'] = $tenant_image_file_ext;
				$data_insert['customer_image_file_size'] = $tenant_image_file_size;
				$data_insert['customer_image_is_image'] = $tenant_image_is_image;
				$data_insert['customer_image_image_width'] = $tenant_image_image_width;
				$data_insert['customer_image_image_height'] = $tenant_image_image_height;
				$data_insert['customer_image_image_type'] = $tenant_image_image_type;
				$data_insert['customer_image_image_size_str'] = $tenant_image_image_size_str;
				
			//cari apakah ada di table rentcar_customer_image
			$this->dbcar->select("*");
			$this->dbcar->from("customer_image");
			$this->dbcar->where("customer_image_customer_id", $tenant_image_tenant_id);
			$q = $this->dbcar->get();
			
			//Jika 0 maka Insert
			if ($q->num_rows == 0) {
				$this->dbcar->insert("customer_image", $data_insert);
			}
			else {
				//Jika ada maka update
				$this->dbcar->update("customer_image", $data_insert);
			}
			
			redirect(base_url()."carmanagement/data_tenant");
			//$this->load->view('carmanagement/upload_success', $data);
		}
	
	}
	
	function remove()
	{
		$customer_company = $this->sess->user_company;
		
		if (!$customer_company){
		redirect(base_url());
		}
		
		$this->dbcar = $this->load->database('rentcar', true);
		$customer_id = $this->uri->segment(3);
		
		$data["customer_flag"] = 1;		
		$this->dbcar->where("customer_id", $customer_id);
		if($this->dbcar->update("rentcar_customer", $data)){
			$callback['message'] = "Data has been deleted";
			$callback['error'] = false;	
		}else{
			$callback['message'] = "Failed delete data";
			$callback['error'] = true;	
		}
		
		//echo json_encode($callback);
		redirect (base_url()."carmanagement/data_tenant/");
	}
	
	function removestatus()
	{
		$settenant_company = $this->sess->user_company;
		
		if (!$settenant_company){
		redirect(base_url());
		}
		
		$this->dbcar = $this->load->database('rentcar', true);
		$settenant_id = $this->uri->segment(3);
		
		$data["settenant_flag"] = 1;		
		$this->dbcar->where("settenant_id", $settenant_id);
		if($this->dbcar->update("rentcar_settenant_vehicle", $data)){
			$callback['message'] = "Data has been deleted";
			$callback['error'] = false;	
		}else{
			$callback['message'] = "Failed delete data";
			$callback['error'] = true;	
		}
		
		//echo json_encode($callback);
		redirect (base_url()."carmanagement/status_vehicle/");
	}

}
