<?php
include "base.php";
class Ppi_hourmeter extends Base {
	function Ppi_hourmeter()
	{
			parent::Base();	
			$this->load->model("gpsmodel");

			if (! isset($this->sess->user_company)){
				redirect(base_url());
			}
			
			$this->load->helper('common_helper');
	}
	
	function index(){
		
		$company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		
		if ($type_admin != 1){
			if (!$company){
			redirect(base_url());
			}
		}
		
		$this->params['sortby'] = "vehicle_name";
		$this->params['orderby'] = "asc";
		$this->params['title'] = "Data Hourmeter List";
				
		$this->params["content"] =  $this->load->view('hourmeter/hourmeter_list', $this->params, true);	
		$this->load->view("templatesess", $this->params);	
	}
	
	function search_hourmeter(){
	
		$company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		$user_group = $this->sess->user_group;
		
		if ($type_admin != 1){
			if (!$company){
			redirect(base_url());
			}
		}
		
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "vehicle_name";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "asc";
		$createdate = isset($_POST['createdate']) ? $_POST['enddate'] : "";
		$createtime = isset($_POST['createtime']) ? $_POST['createtime'] : "";
		$cdate = isset($_POST['cdate']) ? $_POST['cdate'] : "";
		$vehicle_no = isset($_POST['vehicle_no']) ? $_POST['vehicle_no'] : "";
		$vehicle_id = isset($_POST['vehicle_id']) ? $_POST['vehicle_id'] : "";
		$vehicle = isset($_POST['vehicle']) ? $_POST['vehicle'] : 0;
		$vehicle_device = isset($_POST['vehicle_device']) ? $_POST['vehicle_device'] : "";
		$vehicle_name = isset($_POST['vehicle_name']) ? $_POST['vehicle_name'] : "";
		$value = isset($_POST['value']) ? $_POST['value'] : "";
		$value_string = isset($_POST['value_string']) ? $_POST['value_string'] : "";
		$note = isset($_POST['note']) ? $_POST['note'] : "";
		$lastservice = isset($_POST['lastservice']) ? $_POST['lastservice'] : "";
		$lastservice_value = isset($_POST['lastservice_value']) ? $_POST['lastservice_value'] : 0;
	
		//Get vehicle
		$row_vehicle = $this->get_vehicle();
		
		$this->dbtransporter->order_by("data_hm_vehicle_name", "asc");
		$this->dbtransporter->order_by("data_hm_vehicle_no", "asc");
		$this->dbtransporter->where("data_hm_flag", 0);
		
		switch($field)
		{
			case "data_hm_vehicle_no":
				$this->dbtransporter->where("data_hm_vehicle_no LIKE '%".$keyword."%'", null);				
			break;			
			case "data_hm_vehicle_name":
				$this->dbtransporter->where("data_hm_vehicle_name LIKE '%".$keyword."%'", null);				
			break;	
		}
		
		$q = $this->dbtransporter->get("transporter_data_hm_ppi");
		$rows = $q->result();
		//print_r($rows);exit();

		//hitung total
		$this->dbtransporter->select("count(*) as total");
		
		//Get vehicle
		$row_vehicle = $this->get_vehicle();
		
		$this->dbtransporter->order_by("data_hm_vehicle_name", "asc");
		$this->dbtransporter->order_by("data_hm_vehicle_no", "asc");
		$this->dbtransporter->where("data_hm_flag", 0);
		//print_r($vehicle_device);exit();
		switch($field)
		{
			case "data_hm_vehicle_no":
				$this->dbtransporter->where("data_hm_vehicle_no LIKE '%".$keyword."%'", null);				
			break;			
			case "data_hm_vehicle_name":
				$this->dbtransporter->where("data_hm_vehicle_name LIKE '%".$keyword."%'", null);				
			break;	
		}
		
		
		$qt = $this->dbtransporter->get("transporter_data_hm_ppi");
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
		$this->params["vehicle"] = $row_vehicle;

		$html = $this->load->view('hourmeter/hourmeter_list_result', $this->params, true);
		
		$callback['html'] = $html;
		$callback['total'] = $total;
				
		echo json_encode($callback);
	}
	
	function add_hourmeter()
	{
        $company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;

		if ($type_admin != 1){
			if (!$company){
			redirect(base_url());
			}
		}
		
		$this->dbtransporter=$this->load->database("transporter", TRUE);
		
		//get vehicle
		$rows_vehicle = $this->get_vehicle();
		$this->params["rvehicle"] = $rows_vehicle;
	
		$this->params["content"] = $this->load->view('hourmeter/hourmeter_add', $this->params, true);
        $this->load->view("templatesess", $this->params);
	
    }
	
	function save_hourmeter()
	{
	
		$company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		
		if ($type_admin != 1){
			if (!$company){
			redirect(base_url());
			}
		}
		
		$this->dbtransporter=$this->load->database("transporter", TRUE);
		
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "vehicle_name";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "asc";
		$createdate = isset($_POST['createdate']) ? $_POST['createdate'] : "";
		$createtime = isset($_POST['createtime']) ? $_POST['createtime'] : "";
		$cdate = isset($_POST['cdate']) ? $_POST['cdate'] : "";
		$vehicle_no = isset($_POST['vehicle_no']) ? $_POST['vehicle_no'] : "";
		$vehicle_id = isset($_POST['vehicle_id']) ? $_POST['vehicle_id'] : 0;
		$vehicle_device = isset($_POST['vehicle_device']) ? $_POST['vehicle_device'] : "";
		$vehicle_name = isset($_POST['vehicle_name']) ? $_POST['vehicle_name'] : "";
		$value = isset($_POST['value']) ? $_POST['value'] : 0;
		$value_string = isset($_POST['value_string']) ? $_POST['value_string'] : "";
		$note = isset($_POST['note']) ? $_POST['note'] : "";
		$lastservice = isset($_POST['lastservice']) ? $_POST['lastservice'] : "";
		$lastservice_value = isset($_POST['lastservice_value']) ? $_POST['lastservice_value'] : 0;
		$id = isset($_POST['id']) ? $_POST['id'] : 0;	
		$cdate = date("Y-m-d H:i:s", strtotime($createdate . " " . $createtime));
		$now = date("Y-m-d H:i:s");
		$value_hour = "";
		$value_min = "";
		$value_sec = "";
		//dalam detik
		$value_second = $lastservice_value * 3600;
		
							if (isset($value_second))
									{
										$conval = $value_second;
										$seconds = $conval;
										
										// extract hours
										$hours = floor($seconds / (60 * 60));
	 
										// extract minutes
										$divisor_for_minutes = $seconds % (60 * 60);
										$minutes = floor($divisor_for_minutes / 60);
	 
										// extract the remaining seconds
										$divisor_for_seconds = $divisor_for_minutes % 60;
										$seconds = ceil($divisor_for_seconds);
										
										if(isset($hours) && $hours > 0)
										{
											if($hours > 0 && $hours <= 1)
											{
												$value_hour = $hours." "."Hour"." ";
											}
											if($hours >= 2)
											{
												$value_hour = $hours." "."Hours"." ";
											}
										}
										if(isset($minutes) && $minutes > 0)
										{
											if($minutes > 0 && $minutes <= 1 )
											{
												$value_min = $minutes." "."Minute"." ";
											}
											if($minutes >= 2)
											{
												$value_min = $minutes." "."Minutes"." ";
											}
										}
										 if(isset($seconds) && $seconds > 0)
										{
											$value_sec = $seconds." "."Detik"." ";
										}
									}
									
		$value_string = $value_hour." ".$value_min." ".$value_sec;							
		//print_r($value_string);exit(); */
		$error = "";
		if ($vehicle_id == "")
		{
			$error .= "- Please Select Vehicle \n";	
		}else{
			
			$this->dbtransporter->where("data_hm_vehicle_id", $vehicle_id);
			$this->dbtransporter->where("data_hm_flag", 0);
			$qc = $this->dbtransporter->get("transporter_data_hm_ppi");
			if($qc->num_rows() > 0 && $id== 0){
				$error .= "- Vehicle already exist \n";
			
			}
		}
		if ($createdate == "")
		{
			$error .= "- Please Select Date \n";	
		}
		if ($createtime == "")
		{
			$error .= "- Please Select Time \n";	
		}
		
		if ($error != "")
		{
			$callback['error'] = true;	
			$callback['message'] = $error;	
			
			echo json_encode($callback);			
			return;
		}
		
			$this->db->where("vehicle_status <>", 3);
			$this->db->where("vehicle_id", $vehicle_id);
			$qv = $this->db->get("vehicle");
			$vehicles = $qv->row();
			$this->db->close();
			
		unset($data);
			
            $data['data_hm_vehicle_id'] = $vehicle_id;
			$data['data_hm_vehicle_device'] = $vehicles->vehicle_device;
            $data['data_hm_vehicle_name'] = $vehicles->vehicle_name;
			$data['data_hm_vehicle_no'] = $vehicles->vehicle_no;
			$data['data_hm_date'] = $createdate;
			$data['data_hm_time'] = $createtime;
            $data['data_hm_datetime'] = $cdate;
			$data['data_hm_value'] = $value;
            $data['data_hm_note'] = $note;
			$data['data_hm_last_service'] = $lastservice;
			$data['data_hm_last_service_value'] = $lastservice_value;
			$data['data_hm_last_service_string'] = $value_string;
            $this->dbtransporter->insert("transporter_data_hm_ppi", $data);
			
			$callback['error'] = false;	
			$callback['message'] = "Add Data Hourmeter Successfully Submitted";
			$callback["redirect"]= base_url()."transporter/ppi_hourmeter";
			echo json_encode($callback);
			return;
	}
	
	function edit_hourmeter(){
	
		$company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		
		if ($type_admin != 1){
			if (!$company){
			redirect(base_url());
			}
		}
		$id = $this->uri->segment(4);
		$this->dbtransporter=$this->load->database("transporter", TRUE);
		
		//get vehicle
		$rows_vehicle = $this->get_vehicle();
		$this->params["rvehicle"] = $rows_vehicle;
		
		
		if ($id) {
		    $this->dbtransporter->where("data_hm_id", $id);
            $q = $this->dbtransporter->get("transporter_data_hm_ppi");
			
		
			if ($q->num_rows == 0) { return; }
				$row = $q->row();
				//print_r($row);exit();
				$this->params['row'] = $row;
				$this->params['title'] = "Edit Data Hourmeter";
				$this->params['content'] = $this->load->view("hourmeter/hourmeter_edit", $this->params, true);
				$this->load->view("templatesess", $this->params);
				$this->dbtransporter->close();
		} 
		else 
		{
			$this->dbtransporter->close();
			redirect(base_url()."transporter/ppi_hourmeter");
		}
	}
	
	function update_hourmeter(){
		$company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		
		if ($type_admin != 1){
			if (!$company){
			redirect(base_url());
			}
		}
		
		$this->dbtransporter=$this->load->database("transporter", TRUE);
		
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "vehicle_name";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "asc";
		$createdate = isset($_POST['createdate']) ? $_POST['createdate'] : "";
		$createtime = isset($_POST['createtime']) ? $_POST['createtime'] : "";
		$cdate = isset($_POST['cdate']) ? $_POST['cdate'] : "";
		$vehicle_no = isset($_POST['vehicle_no']) ? $_POST['vehicle_no'] : "";
		$vehicle_id = isset($_POST['vehicle_id']) ? $_POST['vehicle_id'] : 0;
		$vehicle_device = isset($_POST['vehicle_device']) ? $_POST['vehicle_device'] : "";
		$vehicle_name = isset($_POST['vehicle_name']) ? $_POST['vehicle_name'] : "";
		$value = isset($_POST['value']) ? $_POST['value'] : "";
		$string = isset($_POST['string']) ? $_POST['string'] : "";
		$note = isset($_POST['note']) ? $_POST['note'] : "";
		$lastservice = isset($_POST['lastservice']) ? $_POST['lastservice'] : "";
		$lastservice_value = isset($_POST['lastservice_value']) ? $_POST['lastservice_value'] : "";
		$value_string = isset($_POST['value_string']) ? $_POST['value_string'] : "";
		$id = isset($_POST['id']) ? $_POST['id'] : 0;	
		$cdate = date("Y-m-d H:i:s", strtotime($createdate . " " . $createtime));
		$now = date("Y-m-d H:i:s");
		$value_hour = "";
		$value_min = "";
		$value_sec = "";
		//dalam detik
		$value_second = $lastservice_value * 3600;
		
							if (isset($value_second))
									{
										$conval = $value_second;
										$seconds = $conval;
										
										// extract hours
										$hours = floor($seconds / (60 * 60));
	 
										// extract minutes
										$divisor_for_minutes = $seconds % (60 * 60);
										$minutes = floor($divisor_for_minutes / 60);
	 
										// extract the remaining seconds
										$divisor_for_seconds = $divisor_for_minutes % 60;
										$seconds = ceil($divisor_for_seconds);
										
										if(isset($hours) && $hours > 0)
										{
											if($hours > 0 && $hours <= 1)
											{
												$value_hour = $hours." "."Hour"." ";
											}
											if($hours >= 2)
											{
												$value_hour = $hours." "."Hours"." ";
											}
										}
										if(isset($minutes) && $minutes > 0)
										{
											if($minutes > 0 && $minutes <= 1 )
											{
												$value_min = $minutes." "."Minute"." ";
											}
											if($minutes >= 2)
											{
												$value_min = $minutes." "."Minutes"." ";
											}
										}
										 if(isset($seconds) && $seconds > 0)
										{
											$value_sec = $seconds." "."Detik"." ";
										}
									}
									
		$value_string = $value_hour." ".$value_min." ".$value_sec;							
		//print_r($value_string);exit(); */
		
		$error = "";
		if ($vehicle_id == "")
		{
			$error .= "- Please Select Vehicle \n";	
		}else{
			
			$this->dbtransporter->where("data_hm_vehicle_id", $vehicle_id);
			$this->dbtransporter->where("data_hm_flag", 0);
			$qc = $this->dbtransporter->get("transporter_data_hm_ppi");
			if($qc->num_rows() > 0 && $id== 0){
				$error .= "- Vehicle already exist \n";
			
			}
		}
		/* if ($vehicle_name == "" || $vehicle_name == 0)
		{
			$error .= "- Please Select Vehicle \n";	
		}
		if ($vehicle_no == "" || $vehicle_no == 0)
		{
			$error .= "- Please Select Vehicle \n";	
		} */
		if ($createdate == "")
		{
			$error .= "- Please Select Date \n";	
		}
		if ($createtime == "")
		{
			$error .= "- Please Select Time \n";	
		}
		
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
		unset($data);

			/* $data['data_hm_vehicle_id'] = $vehicle_id;
			$data['data_hm_vehicle_device'] = $vehicle_device;
            $data['data_hm_vehicle_name'] = $vehicle_name;
			$data['data_hm_vehicle_no'] = $vehicle_no; */
			$data['data_hm_date'] = $createdate;
			$data['data_hm_time'] = $createtime;
            $data['data_hm_datetime'] = $cdate;
			$data['data_hm_value'] = $value;
			$data['data_hm_note'] = $note;
			$data['data_hm_last_service'] = $lastservice;
			$data['data_hm_last_service_value'] = $lastservice_value;
			$data['data_hm_last_service_string'] = $value_string;
			$this->dbtransporter->where("data_hm_id", $id);
            $this->dbtransporter->update("transporter_data_hm_ppi", $data);
			$this->dbtransporter->close();
		
			$callback['error'] = false;
			$callback['message'] = "Hourmeter Successfully Updated";
			$callback['redirect'] = base_url()."transporter/ppi_hourmeter";
			echo json_encode($callback);
			return;
	}
	
	function delete_hourmeter($id)
	{
		$company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		
		if ($type_admin != 1){
			if (!$company){
			redirect(base_url());
			}
		}
		
		$this->dbtransporter=$this->load->database("dbtransporter", TRUE);
		
		$data["data_hm_flag"] = 1;		
		$this->dbtransporter->where("data_hm_id", $id);
		if($this->dbtransporter->update("transporter_data_hm_ppi", $data)){
			$callback['message'] = "Data has been deleted";
			$callback['error'] = false;	
		}else{
			$callback['message'] = "Failed delete data";
			$callback['error'] = true;	
		}
		
		echo json_encode($callback);
	}
	
	function total_hourmeter(){
		
		$company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		
		if ($type_admin != 1){
			if (!$company){
			redirect(base_url());
			}
		}
		
		$this->params['sortby'] = "vehicle_name";
		$this->params['orderby'] = "asc";
		$this->params['title'] = "Total Hourmeter List";
				
		$this->params["content"] =  $this->load->view('hourmeter/total_hourmeter_list', $this->params, true);	
		$this->load->view("templatesess", $this->params);	
	}
	
	function search_total_hourmeter(){
	
		$company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		$user_group = $this->sess->user_group;
		
		if ($type_admin != 1){
			if (!$company){
			redirect(base_url());
			}
		}
		
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "vehicle_name";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "asc";
		$createdate = isset($_POST['createdate']) ? $_POST['enddate'] : "";
		$createtime = isset($_POST['createtime']) ? $_POST['createtime'] : "";
		$cdate = isset($_POST['cdate']) ? $_POST['cdate'] : "";
		$vehicle_no = isset($_POST['vehicle_no']) ? $_POST['vehicle_no'] : "";
		$vehicle_id = isset($_POST['vehicle_id']) ? $_POST['vehicle_id'] : "";
		$vehicle_device = isset($_POST['vehicle_device']) ? $_POST['vehicle_device'] : "";
		$vehicle_name = isset($_POST['vehicle_name']) ? $_POST['vehicle_name'] : "";
		$value = isset($_POST['value']) ? $_POST['value'] : "";
		$value_string = isset($_POST['value_string']) ? $_POST['value_string'] : "";
		$note = isset($_POST['note']) ? $_POST['note'] : "";
	
		//Get vehicle
		$row_vehicle = $this->get_vehicle();
		
		//get total hourmeter
		/* $this->dbtransporter->order_by("data_hm_vehicle_name", "asc");
		$this->dbtransporter->order_by("data_hm_vehicle_no", "asc");
		$this->dbtransporter->where("data_hm_flag", 0);
		$q_hm = $this->dbtransporter->get("transporter_data_hm_ppi");
		$rows_hm = $q_hm->result();
		print_r($rows_hm);exit(); */
		
		$this->dbtransporter->order_by("data_hm_vehicle_name", "asc");
		$this->dbtransporter->order_by("data_hm_vehicle_no", "asc");
		$this->dbtransporter->where("data_hm_flag", 0);
		
		switch($field)
		{
			case "data_hm_vehicle_no":
				$this->dbtransporter->where("data_hm_vehicle_no LIKE '%".$keyword."%'", null);				
			break;			
			case "data_hm_vehicle_name":
				$this->dbtransporter->where("data_hm_vehicle_name LIKE '%".$keyword."%'", null);				
			break;	
		}
		$this->dbtransporter->join("transporter_data_hm_ppi", "data_hm_vehicle_id = data_hm_daily_vehicle_id", "left");
		$q = $this->dbtransporter->get("transporter_data_hm_daily_ppi");
		$rows = $q->result();
		//print_r($rows);exit();

		//hitung total
		$this->dbtransporter->select("count(*) as total");
		
		//Get vehicle
		$row_vehicle = $this->get_vehicle();
		
		$this->dbtransporter->order_by("data_hm_vehicle_name", "asc");
		$this->dbtransporter->order_by("data_hm_vehicle_no", "asc");
		$this->dbtransporter->where("data_hm_flag", 0);
		//print_r($vehicle_device);exit();
		switch($field)
		{
			case "data_hm_vehicle_no":
				$this->dbtransporter->where("data_hm_vehicle_no LIKE '%".$keyword."%'", null);				
			break;			
			case "data_hm_vehicle_name":
				$this->dbtransporter->where("data_hm_vehicle_name LIKE '%".$keyword."%'", null);				
			break;	
		}
		$this->dbtransporter->join("transporter_data_hm_ppi", "data_hm_vehicle_id = data_hm_daily_vehicle_id", "left");
		$qt = $this->dbtransporter->get("transporter_data_hm_daily_ppi");
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
		$this->params["vehicle"] = $row_vehicle;
		//$this->params["data_hm"] = $rows_hm;

		$html = $this->load->view('hourmeter/total_hourmeter_list_result', $this->params, true);
		
		$callback['html'] = $html;
		$callback['total'] = $total;
				
		echo json_encode($callback);
	}
	
	function config(){
		
		$company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		
		if ($type_admin != 1){
			if (!$company){
			redirect(base_url());
			}
		}
		
		$this->params['sortby'] = "vehicle_name";
		$this->params['orderby'] = "asc";
		$this->params['title'] = "Config Hourmeter";
				
		$this->params["content"] =  $this->load->view('hourmeter/config_list', $this->params, true);	
		$this->load->view("templatesess", $this->params);	
	}
	
	function search_config(){
	
		$company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		$user_group = $this->sess->user_group;
		
		if ($type_admin != 1){
			if (!$company){
			redirect(base_url());
			}
		}
		
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "vehicle_name";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "asc";
		$datetime = isset($_POST['datetime']) ? $_POST['datetime'] : "";
		$vehicle_id = isset($_POST['vehicle_id']) ? $_POST['vehicle_id'] : "";
		$vehicle_device = isset($_POST['vehicle_device']) ? $_POST['vehicle_device'] : "";
		$vehicle_name = isset($_POST['vehicle_name']) ? $_POST['vehicle_name'] : "";
		$value = isset($_POST['value']) ? $_POST['value'] : "";
		$value_string = isset($_POST['value_string']) ? $_POST['value_string'] : "";
	
		//Get vehicle
		$row_vehicle = $this->get_vehicle();
		
		$this->dbtransporter->order_by("hm_config_vehicle_name", "asc");
		$this->dbtransporter->order_by("hm_config_vehicle_no", "asc");
		$this->dbtransporter->where("hm_config_flag", 0);
		
		switch($field){
		case "vehicle_no":
			$this->dbtransporter->where("hm_config_vehicle_no LIKE '%".$keyword."%'", null);
		break;
		case "vehicle_name":
			$this->dbtransporter->where("hm_config_vehicle_name LIKE '%".$keyword."%'", null);
		break;
		case "value":
			$this->dbtransporter->where("hm_config_value LIKE '%".$keyword."%'", null);
		break;
		}
		
		$q = $this->dbtransporter->get("transporter_data_hm_config_ppi", $this->config->item("limit_records_ppi"), $offset);
		$rows = $q->result();
		//print_r($rows);exit();

		//hitung total
		$this->dbtransporter->select("count(*) as total");
		
		//Get vehicle
		$row_vehicle = $this->get_vehicle();
		
		$this->dbtransporter->order_by("hm_config_vehicle_name", "asc");
		$this->dbtransporter->order_by("hm_config_vehicle_no", "asc");
		$this->dbtransporter->where("hm_config_flag", 0);
		
		switch($field){
		case "vehicle_no":
			$this->dbtransporter->where("hm_config_vehicle_no LIKE '%".$keyword."%'", null);
		break;
		case "vehicle_name":
			$this->dbtransporter->where("hm_config_vehicle_name LIKE '%".$keyword."%'", null);
		break;
		case "value":
			$this->dbtransporter->where("hm_config_value LIKE '%".$keyword."%'", null);
		break;
		}
		
		
		$qt = $this->dbtransporter->get("transporter_data_hm_config_ppi");
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
		$this->params["vehicle"] = $row_vehicle;

		$html = $this->load->view('hourmeter/config_list_result', $this->params, true);
		
		$callback['html'] = $html;
		$callback['total'] = $total;
				
		echo json_encode($callback);
	}
	
	function add_config()
	{
        $company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;

		if ($type_admin != 1){
			if (!$company){
			redirect(base_url());
			}
		}
		
		$this->dbtransporter=$this->load->database("transporter", TRUE);
		
		//get vehicle
		$rows_vehicle = $this->get_vehicle();
		$this->params["rvehicle"] = $rows_vehicle;
	
		$this->params["content"] = $this->load->view('hourmeter/config_add', $this->params, true);
        $this->load->view("templatesess", $this->params);
	
    }
	
	function save_config()
	{
	
		$company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		
		if ($type_admin != 1){
			if (!$company){
			redirect(base_url());
			}
		}
		
		$this->dbtransporter=$this->load->database("transporter", TRUE);
		
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "vehicle_name";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "asc";
		$datetime = isset($_POST['datetime']) ? $_POST['datetime'] : "";
		$vehicle_no = isset($_POST['vehicle_no']) ? $_POST['vehicle_no'] : "";
		$vehicle_id = isset($_POST['vehicle_id']) ? $_POST['vehicle_id'] : 0;
		$vehicle_device = isset($_POST['vehicle_device']) ? $_POST['vehicle_device'] : "";
		$vehicle_name = isset($_POST['vehicle_name']) ? $_POST['vehicle_name'] : "";
		$value = isset($_POST['value']) ? $_POST['value'] : 0;
		$value_string = isset($_POST['value_string']) ? $_POST['value_string'] : "";
		$id = isset($_POST['id']) ? $_POST['id'] : 0;	
		$now = date("Y-m-d H:i:s");
		$error = "";
		if ($vehicle_id == "")
		{
			$error .= "- Please Select Vehicle \n";	
		}else{
			
			$this->dbtransporter->where("hm_config_vehicle_id", $vehicle_id);
			$this->dbtransporter->where("hm_config_flag", 0);
			$qc = $this->dbtransporter->get("transporter_data_hm_config_ppi");
			if($qc->num_rows() > 0 && $id== 0){
				$error .= "- Vehicle already exist \n";
			
			}
		}
		/* if ($datetime == "")
		{
			$error .= "- Please Select Datetime \n";	
		} */
		
		
		if ($error != "")
		{
			$callback['error'] = true;	
			$callback['message'] = $error;	
			
			echo json_encode($callback);			
			return;
		}
		unset($data);
		
            $data['hm_config_vehicle_id'] = $vehicle_id;
			$data['hm_config_vehicle_device'] = $vehicle_device;
            $data['hm_config_vehicle_name'] = $vehicle_name;
			$data['hm_config_vehicle_no'] = $vehicle_no;
			$data['hm_config_datetime'] = $now;
			$data['hm_config_value'] = $value;
            $data['hm_config_string'] = $value_string;
            $this->dbtransporter->insert("transporter_data_hm_config_ppi", $data);
			
			$callback['error'] = false;	
			$callback['message'] = "Add Config Hourmeter Successfully Submitted";
			$callback["redirect"]= base_url()."transporter/ppi_hourmeter/config/";
			echo json_encode($callback);
			return;
	}
	
	function edit_config(){
	
		$company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		
		if ($type_admin != 1){
			if (!$company){
			redirect(base_url());
			}
		}
		$id = $this->uri->segment(4);
		$this->dbtransporter=$this->load->database("transporter", TRUE);
		
		//get vehicle
		$rows_vehicle = $this->get_vehicle();
		$this->params["rvehicle"] = $rows_vehicle;
		
		
		if ($id) {
		    $this->dbtransporter->where("hm_config_id", $id);
            $q = $this->dbtransporter->get("transporter_data_hm_config_ppi");
			
		
			if ($q->num_rows == 0) { return; }
				$row = $q->row();
				//print_r($row);exit();
				$this->params['row'] = $row;
				$this->params['title'] = "Edit Config Hourmeter";
				$this->params['content'] = $this->load->view("hourmeter/config_edit", $this->params, true);
				$this->load->view("templatesess", $this->params);
				$this->dbtransporter->close();
		} 
		else 
		{
			$this->dbtransporter->close();
			redirect(base_url()."transporter/ppi_hourmeter/config/");
		}
	}
	
	function update_config(){
		$company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		
		if ($type_admin != 1){
			if (!$company){
			redirect(base_url());
			}
		}
		
		$this->dbtransporter=$this->load->database("transporter", TRUE);
		
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "vehicle_name";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "asc";
		$datetime = isset($_POST['datetime']) ? $_POST['datetime'] : "";
		$vehicle_no = isset($_POST['vehicle_no']) ? $_POST['vehicle_no'] : "";
		$vehicle_id = isset($_POST['vehicle_id']) ? $_POST['vehicle_id'] : 0;
		$vehicle_device = isset($_POST['vehicle_device']) ? $_POST['vehicle_device'] : "";
		$vehicle_name = isset($_POST['vehicle_name']) ? $_POST['vehicle_name'] : "";
		$value = isset($_POST['value']) ? $_POST['value'] : 0;
		$value_string = isset($_POST['value_string']) ? $_POST['value_string'] : "";
		$id = isset($_POST['id']) ? $_POST['id'] : 0;	
		$now = date("Y-m-d H:i:s");
		
		$error = "";
		if ($vehicle_id == "")
		{
			$error .= "- Please Select Vehicle \n";	
		}else{
			
			$this->dbtransporter->where("hm_config_vehicle_id", $vehicle_id);
			$this->dbtransporter->where("hm_config_flag", 0);
			$qc = $this->dbtransporter->get("transporter_data_hm_config_ppi");
			if($qc->num_rows() > 0 && $id== 0){
				$error .= "- Vehicle already exist \n";
			
			}
		}
		
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
		unset($data);

			/* $data['hm_config_vehicle_device'] = $vehicle_device;
            $data['hm_config_vehicle_name'] = $vehicle_name;
			$data['hm_config_vehicle_no'] = $vehicle_no; */
			$data['hm_config_datetime'] = $now;
			$data['hm_config_value'] = $value;
            $data['hm_config_string'] = $value_string;
			
			$this->dbtransporter->where("hm_config_id", $id);
            $this->dbtransporter->update("transporter_data_hm_config_ppi", $data);
			$this->dbtransporter->close();
		
			$callback['error'] = false;
			$callback['message'] = "Config Hourmeter Successfully Updated";
			$callback['redirect'] = base_url()."transporter/ppi_hourmeter/config/";
			echo json_encode($callback);
			return;
	}
	
	function alert(){
		
		$company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		
		if ($type_admin != 1){
			if (!$company){
			redirect(base_url());
			}
		}
		
		$this->params['sortby'] = "hm_alert_vehicle_name";
		$this->params['orderby'] = "asc";
		$this->params['title'] = "Alert Hourmeter List";
				
		$this->params["content"] =  $this->load->view('hourmeter/alert_list', $this->params, true);	
		$this->load->view("templatesess", $this->params);	
	}
	
	function search_alert(){
	
		$company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		$user_group = $this->sess->user_group;
		
		if ($type_admin != 1){
			if (!$company){
			redirect(base_url());
			}
		}
		
		//Process Alert
		//$process_alert = $this->process_alert();
		
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "hm_alert_vehicle_name";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "asc";
		$datetime = isset($_POST['datetime']) ? $_POST['datetime'] : "";
		$vehicle_no = isset($_POST['vehicle_no']) ? $_POST['vehicle_no'] : "";
		$vehicle_id = isset($_POST['vehicle_id']) ? $_POST['vehicle_id'] : "";
		$vehicle_device = isset($_POST['vehicle_device']) ? $_POST['vehicle_device'] : "";
		$vehicle_name = isset($_POST['vehicle_name']) ? $_POST['vehicle_name'] : "";
		$value = isset($_POST['value']) ? $_POST['value'] : "";
		$value_string = isset($_POST['value_string']) ? $_POST['value_string'] : "";
		$note = isset($_POST['note']) ? $_POST['note'] : "";
		$process = isset($_POST['process']) ? $_POST['process'] : 0;
		$status = isset($_POST['status']) ? $_POST['status'] : 0;
	
		//Get vehicle
		$row_vehicle = $this->get_vehicle();
		
		$this->dbtransporter->order_by("hm_alert_vehicle_name", "asc");
		$this->dbtransporter->order_by("hm_alert_vehicle_no", "asc");
		$this->dbtransporter->where("hm_alert_flag", 0);
		
		switch($field)
		{
			case "hm_alert_vehicle_no":
				$this->dbtransporter->where("hm_alert_vehicle_no LIKE '%".$keyword."%'", null);				
			break;			
			case "hm_alert_vehicle_name":
				$this->dbtransporter->where("hm_alert_vehicle_name LIKE '%".$keyword."%'", null);				
			break;	
		}
		$this->dbtransporter->join("transporter_data_hm_ppi", "hm_alert_vehicle_id = data_hm_vehicle_id", "left");
		$this->dbtransporter->join("transporter_data_hm_daily_ppi", "hm_alert_vehicle_id = data_hm_daily_vehicle_id", "left");
		$q = $this->dbtransporter->get("transporter_data_hm_alert_ppi", $this->config->item("limit_records_ppi"), $offset);
		$rows = $q->result();
		//print_r($rows);exit();

		//hitung total
		$this->dbtransporter->select("count(*) as total");
		
		//Get vehicle
		$row_vehicle = $this->get_vehicle();
		
		$this->dbtransporter->order_by("hm_alert_vehicle_name", "asc");
		$this->dbtransporter->order_by("hm_alert_vehicle_no", "asc");
		$this->dbtransporter->where("hm_alert_flag", 0);
		
		switch($field)
		{
			case "hm_alert_vehicle_no":
				$this->dbtransporter->where("hm_alert_vehicle_no LIKE '%".$keyword."%'", null);				
			break;			
			case "hm_alert_vehicle_name":
				$this->dbtransporter->where("hm_alert_vehicle_name LIKE '%".$keyword."%'", null);				
			break;	
		}
		
		$this->dbtransporter->join("transporter_data_hm_ppi", "hm_alert_vehicle_id = data_hm_vehicle_id", "left");
		$this->dbtransporter->join("transporter_data_hm_daily_ppi", "hm_alert_vehicle_id = data_hm_daily_vehicle_id", "left");
		$qt = $this->dbtransporter->get("transporter_data_hm_alert_ppi");
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
		$this->params["vehicle"] = $row_vehicle;

		$html = $this->load->view('hourmeter/alert_list_result', $this->params, true);
		
		$callback['html'] = $html;
		$callback['total'] = $total;
				
		echo json_encode($callback);
	}
	
	function edit_alert(){
	
		$company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		
		if ($type_admin != 1){
			if (!$company){
			redirect(base_url());
			}
		}
		$vehicle_id = $this->uri->segment(4);
		$this->dbtransporter=$this->load->database("transporter", TRUE);
		
		//get vehicle
		$rows_vehicle = $this->get_vehicle();
		$this->params["rvehicle"] = $rows_vehicle;
		
		
		if ($vehicle_id) {
			$this->dbtransporter->join("transporter_data_hm_alert_ppi", "data_hm_vehicle_id = hm_alert_vehicle_id", "left");
		    $this->dbtransporter->where("data_hm_vehicle_id", $vehicle_id);
            $q = $this->dbtransporter->get("transporter_data_hm_ppi");
			
		
			if ($q->num_rows == 0) { return; }
				$row = $q->row();
				//print_r($row);exit();
				$this->params['row'] = $row;
				$this->params['title'] = "Update Last Service Hourmeter";
				$this->params['content'] = $this->load->view("hourmeter/alert_edit", $this->params, true);
				$this->load->view("templatesess", $this->params);
				$this->dbtransporter->close();
		} 
		else 
		{
			$this->dbtransporter->close();
			redirect(base_url()."transporter/ppi_hourmeter/alert");
		}
	}
	
	function update_alert(){
		$company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		
		if ($type_admin != 1){
			if (!$company){
			redirect(base_url());
			}
		}
		
		$this->dbtransporter=$this->load->database("transporter", TRUE);
		
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "vehicle_name";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "asc";
		/* $createdate = isset($_POST['createdate']) ? $_POST['createdate'] : "";
		$createtime = isset($_POST['createtime']) ? $_POST['createtime'] : ""; */
		//$cdate = isset($_POST['cdate']) ? $_POST['cdate'] : "";
		$vehicle_no = isset($_POST['vehicle_no']) ? $_POST['vehicle_no'] : "";
		$vehicle_id = isset($_POST['vehicle_id']) ? $_POST['vehicle_id'] : 0;
		$vehicle_device = isset($_POST['vehicle_device']) ? $_POST['vehicle_device'] : "";
		$vehicle_name = isset($_POST['vehicle_name']) ? $_POST['vehicle_name'] : "";
		$value = isset($_POST['value']) ? $_POST['value'] : "";
		$string = isset($_POST['string']) ? $_POST['string'] : "";
		$value_string = isset($_POST['value_string']) ? $_POST['value_string'] : "";
		$note = isset($_POST['note']) ? $_POST['note'] : "";
		$lastservice = isset($_POST['lastservice']) ? $_POST['lastservice'] : "";
		$lastservice_value = isset($_POST['lastservice_value']) ? $_POST['lastservice_value'] : "";
		$id = isset($_POST['id']) ? $_POST['id'] : 0;	
		//$cdate = date("Y-m-d H:i:s", strtotime($createdate . " " . $createtime));
		$now = date("Y-m-d H:i:s");
		
		//print_r($sdate." s/d ".$edate." status: ".$vehicle_status);exit();
		$error = "";
		if ($vehicle_id == "")
		{
			$error .= "- Please Select Vehicle \n";	
		}
		/* if ($vehicle_name == "" || $vehicle_name == 0)
		{
			$error .= "- Please Select Vehicle \n";	
		}
		if ($vehicle_no == "" || $vehicle_no == 0)
		{
			$error .= "- Please Select Vehicle \n";	
		}
		if ($vehicle_device == "" || $vehicle_device == 0)
		{
			$error .= "- Please Select Vehicle \n";	
		} */
		if ($lastservice == "" || $lastservice == 0)
		{
			$error .= "- Please Select Date \n";	
		}
		/* if ($lastservice_value == "" || $lastservice_value == 0)
		{
			$error .= "- Please Input Houmeter \n";	
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
			unset($data);
			$data['data_hm_last_service'] = $lastservice;
			$data['data_hm_last_service_value'] = $value;
			$data['data_hm_last_service_string'] = $value_string;
			
			$this->dbtransporter->where("data_hm_vehicle_id", $vehicle_id);
            $this->dbtransporter->update("transporter_data_hm_ppi", $data);
			$this->dbtransporter->close();
		
			unset($data_svc);
			$data_svc['hm_service_vehicle_id'] = $vehicle_id;
			$data_svc['hm_service_vehicle_device'] = $vehicle_device;
			$data_svc['hm_service_vehicle_name'] = $vehicle_name;
			$data_svc['hm_service_vehicle_no'] = $vehicle_no;
			$data_svc['hm_service_value'] = $value;
			$data_svc['hm_service_string'] = $value_string;
			$data_svc['hm_service_datetime'] = $lastservice;
			$data_svc['hm_service_note'] = $note;
			
            $this->dbtransporter->insert("transporter_data_hm_service_ppi", $data_svc);
			$this->dbtransporter->close();
			
			unset($data_alert);
			$process = 1;
			$status = 0;
			$data_alert['hm_alert_process'] = $process;
			$data_alert['hm_alert_status'] = 0;
			//print_r("disini");exit();
			$this->dbtransporter->where("hm_alert_vehicle_id", $vehicle_id);
            $this->dbtransporter->update("transporter_data_hm_alert_ppi", $data_alert);
			$this->dbtransporter->close();
		
			$callback['error'] = false;
			$callback['message'] = "Last Service Hourmeter Successfully Updated";
			$callback['redirect'] = base_url()."transporter/ppi_hourmeter/alert";
			echo json_encode($callback);
			return;
	}
	
	function get_vehicle(){
		$customer_company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		
		if ($type_admin != 1){
			if (!$customer_company){
			redirect(base_url());
			}
		}
		
		$this->db->order_by("vehicle_name", "asc");
		if ($type_admin != 1){
			$this->db->where("vehicle_user_id", $this->sess->user_id);
		} 
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		$q = $this->db->get("vehicle");
		$row_vehicle = $q->result();
        
		$data_vehicle = $row_vehicle;
		//print_r($data_vehicle);exit();
        $this->db->cache_delete_all();
        return $data_vehicle;
	}
	
	function get_lastservice_hm(){
		$customer_company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		
		if ($type_admin != 1){
			if (!$customer_company){
			redirect(base_url());
			}
		}
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		
		$this->dbtransporter->order_by("data_hm_vehicle_id", "asc");
		$this->dbtransporter->where("data_hm_flag <>", 0);
		$q = $this->dbtransporter->get("transporter_data_hm_ppi");
		$row_lastservice = $q->result();
        
		$data_lastservice = $row_lastservice;
		
        $this->dbtransporter->cache_delete_all();
        return $data_lastservice;
	}
	
	function service(){
	
		$company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		
		if ($type_admin != 1){
			if (!$company){
			redirect(base_url());
			}
		}
		
		$this->params['sortby'] = "vehicle_name";
		$this->params['orderby'] = "asc";
		$this->params['title'] = "Data Service HM";
				
		$this->params["content"] =  $this->load->view('hourmeter/service_list', $this->params, true);	
		$this->load->view("templatesess", $this->params);
	}
	
	function search_service(){
		
		$company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		$user_group = $this->sess->user_group;
		
		if ($type_admin != 1){
			if (!$company){
			redirect(base_url());
			}
		}
		
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "hm_service_date";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "desc";
		$createdate = isset($_POST['createdate']) ? $_POST['enddate'] : "";
		$createtime = isset($_POST['createtime']) ? $_POST['createtime'] : "";
		$cdate = isset($_POST['cdate']) ? $_POST['cdate'] : "";
		$vehicle_no = isset($_POST['vehicle_no']) ? $_POST['vehicle_no'] : "";
		$vehicle_id = isset($_POST['vehicle_id']) ? $_POST['vehicle_id'] : "";
		$vehicle = isset($_POST['vehicle']) ? $_POST['vehicle'] : 0;
		$vehicle_device = isset($_POST['vehicle_device']) ? $_POST['vehicle_device'] : "";
		$vehicle_name = isset($_POST['vehicle_name']) ? $_POST['vehicle_name'] : "";
		$value = isset($_POST['value']) ? $_POST['value'] : "";
		$value_string = isset($_POST['value_string']) ? $_POST['value_string'] : "";
		$note = isset($_POST['note']) ? $_POST['note'] : "";
		$lastservice = isset($_POST['lastservice']) ? $_POST['lastservice'] : "";
		$lastservice_value = isset($_POST['lastservice_value']) ? $_POST['lastservice_value'] : 0;
	
		//Get vehicle
		$row_vehicle = $this->get_vehicle();
		
		$this->dbtransporter->order_by("hm_service_datetime", "desc");
		$this->dbtransporter->where("hm_service_flag", 0);
		
		switch($field)
		{
			case "hm_service_vehicle_no":
				$this->dbtransporter->where("hm_service_vehicle_no LIKE '%".$keyword."%'", null);				
			break;			
			case "hm_service_vehicle_name":
				$this->dbtransporter->where("hm_service_vehicle_name LIKE '%".$keyword."%'", null);				
			break;	
		}
		
		$q = $this->dbtransporter->get("transporter_data_hm_service_ppi", $this->config->item("limit_records_ppi"), $offset);
		$rows = $q->result();
		//print_r($rows);exit();

		//hitung total
		$this->dbtransporter->select("count(*) as total");
		
		//Get vehicle
		$row_vehicle = $this->get_vehicle();
		
		$this->dbtransporter->order_by("hm_service_datetime", "desc");
		$this->dbtransporter->where("hm_service_flag", 0);
		//print_r($vehicle_device);exit();
		switch($field)
		{
			case "hm_service_vehicle_no":
				$this->dbtransporter->where("hm_service_vehicle_no LIKE '%".$keyword."%'", null);				
			break;			
			case "hm_service_vehicle_name":
				$this->dbtransporter->where("hm_service_vehicle_name LIKE '%".$keyword."%'", null);				
			break;	
		}
		
		
		$qt = $this->dbtransporter->get("transporter_data_hm_service_ppi");
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
		$this->params["vehicle"] = $row_vehicle;

		$html = $this->load->view('hourmeter/service_list_result', $this->params, true);
		
		$callback['html'] = $html;
		$callback['total'] = $total;
				
		echo json_encode($callback);
		
	}
	
	function add_service(){
        $company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;

		if ($type_admin != 1){
			if (!$company){
			redirect(base_url());
			}
		}
		
		$this->dbtransporter=$this->load->database("transporter", TRUE);
		
		//get vehicle
		$rows_vehicle = $this->get_vehicle();
		$this->params["rvehicle"] = $rows_vehicle;
	
		$this->params["content"] = $this->load->view('hourmeter/service_add', $this->params, true);
        $this->load->view("templatesess", $this->params);
	
    }
	
	function save_service()	{
	
		$company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		
		if ($type_admin != 1){
			if (!$company){
			redirect(base_url());
			}
		}
		
		$this->dbtransporter=$this->load->database("transporter", TRUE);
		
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "vehicle_name";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "asc";
		$vehicle_no = isset($_POST['vehicle_no']) ? $_POST['vehicle_no'] : "";
		$vehicle_id = isset($_POST['vehicle_id']) ? $_POST['vehicle_id'] : 0;
		$vehicle_device = isset($_POST['vehicle_device']) ? $_POST['vehicle_device'] : "";
		$vehicle_name = isset($_POST['vehicle_name']) ? $_POST['vehicle_name'] : "";
		$note = isset($_POST['note']) ? $_POST['note'] : "";
		$lastservice = isset($_POST['lastservice']) ? $_POST['lastservice'] : "";
		$lastservice_value = isset($_POST['lastservice_value']) ? $_POST['lastservice_value'] : 0;
		$id = isset($_POST['id']) ? $_POST['id'] : 0;
		$lastservice_hour = "00:00:00";
		$sdatetime = date("Y-m-d H:i:s", strtotime($lastservice . " " . $lastservice_hour));
		$now = date("Y-m-d H:i:s");
		$update_hm = isset($_POST['update_hm']) ? $_POST['update_hm'] : 0;
		$value_hour = "";
		$value_min = "";
		$value_sec = "";
		//dalam detik
		$value_second = $lastservice_value * 3600;
		
							if (isset($value_second))
									{
										$conval = $value_second;
										$seconds = $conval;
										
										// extract hours
										$hours = floor($seconds / (60 * 60));
	 
										// extract minutes
										$divisor_for_minutes = $seconds % (60 * 60);
										$minutes = floor($divisor_for_minutes / 60);
	 
										// extract the remaining seconds
										$divisor_for_seconds = $divisor_for_minutes % 60;
										$seconds = ceil($divisor_for_seconds);
										
										if(isset($hours) && $hours > 0)
										{
											if($hours > 0 && $hours <= 1)
											{
												$value_hour = $hours." "."Hour"." ";
											}
											if($hours >= 2)
											{
												$value_hour = $hours." "."Hours"." ";
											}
										}
										if(isset($minutes) && $minutes > 0)
										{
											if($minutes > 0 && $minutes <= 1 )
											{
												$value_min = $minutes." "."Minute"." ";
											}
											if($minutes >= 2)
											{
												$value_min = $minutes." "."Minutes"." ";
											}
										}
										 if(isset($seconds) && $seconds > 0)
										{
											$value_sec = $seconds." "."Detik"." ";
										}
									}
									
		$value_string = $value_hour." ".$value_min." ".$value_sec;							
		//print_r($value_string);exit(); */
		$error = "";
		if ($vehicle_id == "")
		{
			$error .= "- Please Select Vehicle \n";	
		}
		
		if ($lastservice == "")
		{
			$error .= "- Please Select Date \n";	
		}
		if ($lastservice_value == "")
		{
			$error .= "- Please Input Hourmeter \n";	
		}
		
		if ($error != "")
		{
			$callback['error'] = true;	
			$callback['message'] = $error;	
			
			echo json_encode($callback);			
			return;
		}
			$this->db->where("vehicle_status <>", 3);
			$this->db->where("vehicle_id", $vehicle_id);
			$qv = $this->db->get("vehicle");
			$vehicles = $qv->row();
			$this->db->close();
			
		unset($data);
		
            $data['hm_service_vehicle_id'] = $vehicle_id;
			$data['hm_service_vehicle_device'] = $vehicles->vehicle_device;
            $data['hm_service_vehicle_name'] = $vehicles->vehicle_name;
			$data['hm_service_vehicle_no'] = $vehicles->vehicle_no;
            $data['hm_service_datetime'] = $sdatetime;
			$data['hm_service_value'] = $lastservice_value;
			$data['hm_service_string'] = $value_string;
			$data['hm_service_note'] = $note;
            $this->dbtransporter->insert("transporter_data_hm_service_ppi", $data);
			
			if($update_hm != 0){
			
				unset($data_hm);
				
				$data_hm['data_hm_last_service_value'] = $lastservice_value;
				$data_hm['data_hm_last_service_string'] = $value_string;
				$data_hm['data_hm_last_service'] = $lastservice;
				
				$this->dbtransporter->where("data_hm_vehicle_id", $vehicle_id);
				$this->dbtransporter->update("transporter_data_hm_ppi", $data_hm);
				$this->dbtransporter->close();
				
				unset($data_alert);
				$process = 1;
				$status = 0;
				$data_alert['hm_alert_process'] = $process;
				$data_alert['hm_alert_status'] = 0;
				
				$this->dbtransporter->where("hm_alert_vehicle_id", $vehicle_id);
				$this->dbtransporter->update("transporter_data_hm_alert_ppi", $data_alert);
				$this->dbtransporter->close();
			
			}

			$callback['error'] = false;	
			$callback['message'] = "Add Data Service Successfully Submitted";
			$callback["redirect"]= base_url()."transporter/ppi_hourmeter/service";
			echo json_encode($callback);
			return;
	}
	
	function edit_service(){
	
		$company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		
		if ($type_admin != 1){
			if (!$company){
			redirect(base_url());
			}
		}
		$id = $this->uri->segment(4);
		$this->dbtransporter=$this->load->database("transporter", TRUE);
		
		//get vehicle
		$rows_vehicle = $this->get_vehicle();
		$this->params["rvehicle"] = $rows_vehicle;
		
		
		if ($id) {
		    $this->dbtransporter->where("hm_service_id", $id);
            $q = $this->dbtransporter->get("transporter_data_hm_service_ppi");
			
		
			if ($q->num_rows == 0) { return; }
				$row = $q->row();
				//print_r($row);exit();
				$this->params['row'] = $row;
				$this->params['title'] = "Edit Data Service";
				$this->params['content'] = $this->load->view("hourmeter/service_edit", $this->params, true);
				$this->load->view("templatesess", $this->params);
				$this->dbtransporter->close();
		} 
		else 
		{
			$this->dbtransporter->close();
			redirect(base_url()."transporter/ppi_hourmeter/service");
		}
	}
	
	function update_service(){
		$company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		
		if ($type_admin != 1){
			if (!$company){
			redirect(base_url());
			}
		}
		
		$this->dbtransporter=$this->load->database("transporter", TRUE);
		
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "vehicle_name";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "asc";
		$vehicle_no = isset($_POST['vehicle_no']) ? $_POST['vehicle_no'] : "";
		$vehicle_id = isset($_POST['vehicle_id']) ? $_POST['vehicle_id'] : 0;
		$vehicle_device = isset($_POST['vehicle_device']) ? $_POST['vehicle_device'] : "";
		$vehicle_name = isset($_POST['vehicle_name']) ? $_POST['vehicle_name'] : "";
		$note = isset($_POST['note']) ? $_POST['note'] : "";
		$lastservice = isset($_POST['lastservice']) ? $_POST['lastservice'] : "";
		$lastservice_value = isset($_POST['lastservice_value']) ? $_POST['lastservice_value'] : 0;
		$id = isset($_POST['id']) ? $_POST['id'] : 0;
		$lastservice_hour = "00:00:00";
		$sdatetime = date("Y-m-d H:i:s", strtotime($lastservice . " " . $lastservice_hour));
		$now = date("Y-m-d H:i:s");
		$update_hm = isset($_POST['update_hm']) ? $_POST['update_hm'] : 0;
		$value_hour = "";
		$value_min = "";
		$value_sec = "";
		//dalam detik
		$value_second = $lastservice_value * 3600;
		
							if (isset($value_second))
									{
										$conval = $value_second;
										$seconds = $conval;
										
										// extract hours
										$hours = floor($seconds / (60 * 60));
	 
										// extract minutes
										$divisor_for_minutes = $seconds % (60 * 60);
										$minutes = floor($divisor_for_minutes / 60);
	 
										// extract the remaining seconds
										$divisor_for_seconds = $divisor_for_minutes % 60;
										$seconds = ceil($divisor_for_seconds);
										
										if(isset($hours) && $hours > 0)
										{
											if($hours > 0 && $hours <= 1)
											{
												$value_hour = $hours." "."Hour"." ";
											}
											if($hours >= 2)
											{
												$value_hour = $hours." "."Hours"." ";
											}
										}
										if(isset($minutes) && $minutes > 0)
										{
											if($minutes > 0 && $minutes <= 1 )
											{
												$value_min = $minutes." "."Minute"." ";
											}
											if($minutes >= 2)
											{
												$value_min = $minutes." "."Minutes"." ";
											}
										}
										 if(isset($seconds) && $seconds > 0)
										{
											$value_sec = $seconds." "."Detik"." ";
										}
									}
									
		$value_string = $value_hour." ".$value_min." ".$value_sec;							
		//print_r($value_string);exit(); */
		
		$error = "";
		if ($vehicle_id == "")
		{
			$error .= "- Please Select Vehicle \n";	
		}
		
		if ($lastservice == "")
		{
			$error .= "- Please Select Date \n";	
		}
		if ($lastservice_value == "")
		{
			$error .= "- Please Input Hourmeter \n";	
		}
		
		if ($error != "")
		{
			$callback['error'] = true;	
			$callback['message'] = $error;	
			
			echo json_encode($callback);			
			return;
		}
			$this->db->where("vehicle_status <>", 3);
			$this->db->where("vehicle_id", $vehicle_id);
			$qv = $this->db->get("vehicle");
			$vehicles = $qv->row();
			$this->db->close();
			
		unset($data);

			$data['hm_service_vehicle_id'] = $vehicle_id;
			$data['hm_service_vehicle_device'] = $vehicles->vehicle_device;
            $data['hm_service_vehicle_name'] = $vehicles->vehicle_name;
			$data['hm_service_vehicle_no'] = $vehicles->vehicle_no;
            $data['hm_service_datetime'] = $sdatetime;
			$data['hm_service_value'] = $lastservice_value;
			$data['hm_service_string'] = $value_string;
			$data['hm_service_note'] = $note;
			$this->dbtransporter->where("hm_service_id", $id);
            $this->dbtransporter->update("transporter_data_hm_service_ppi", $data);
			$this->dbtransporter->close();
			
			if($update_hm != 0){
			
				unset($data_hm);
				
				$data_hm['data_hm_last_service_value'] = $lastservice_value;
				$data_hm['data_hm_last_service_string'] = $value_string;
				$data_hm['data_hm_last_service'] = $lastservice;
				
				$this->dbtransporter->where("data_hm_vehicle_id", $vehicle_id);
				$this->dbtransporter->update("transporter_data_hm_ppi", $data_hm);
				$this->dbtransporter->close();
				
				unset($data_alert);
				$process = 1;
				$status = 0;
				$data_alert['hm_alert_process'] = $process;
				$data_alert['hm_alert_status'] = 0;
				
				$this->dbtransporter->where("hm_alert_vehicle_id", $vehicle_id);
				$this->dbtransporter->update("transporter_data_hm_alert_ppi", $data_alert);
				$this->dbtransporter->close();
			
			}
		
			$callback['error'] = false;
			$callback['message'] = "Data Service Successfully Updated";
			$callback['redirect'] = base_url()."transporter/ppi_hourmeter/service";
			echo json_encode($callback);
			return;
	}
	
	function delete_service($id)
	{
		$company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		
		if ($type_admin != 1){
			if (!$company){
			redirect(base_url());
			}
		}
		
		$this->dbtransporter=$this->load->database("transporter", TRUE);
		
		$data["hm_service_flag"] = 1;		
		$this->dbtransporter->where("hm_service_id", $id);
		if($this->dbtransporter->update("transporter_data_hm_service_ppi", $data)){
			$callback['message'] = "Data has been deleted";
			$callback['error'] = false;	
		}else{
			$callback['message'] = "Failed delete data";
			$callback['error'] = true;	
		}
		
		echo json_encode($callback);
	}
	
	
	
	function process_alert($startdate= "", $enddate= ""){
		
		$start_time = date("d-m-Y H:i:s");
		$offset = 0;
		$i = 0;

		if ($startdate == "") {
            $startdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
        }

        
        if ($enddate == "") {
            $enddate = date("Y-m-d 23:59:59", strtotime("yesterday"));
			//$enddate = date("2013-12-23 23:59:59");
        }
		
		$this->db->order_by("vehicle_device", "desc");
		$this->db->where("vehicle_user_id", "1839");
		$this->db->where("vehicle_status <>", "3");
		$q = $this->db->get('vehicle');
		$vehicle = $q->result();
		$totalv = count($vehicle);
		
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by("data_hm_vehicle_device", "desc");
		$this->dbtransporter->where("data_hm_flag", 0);
		$q_lastservice = $this->dbtransporter->get("data_hm_ppi");
		$row_lastservice = $q_lastservice->result();
		//print_r($row_lastservice);exit();
		for ($x=0;$x<count($vehicle);$x++)
		{
			if (($i+1) < $offset)
			{
				$i++;
				continue;
			}
		
			//print_r($vehicle);exit;
			if (isset($vehicle[$x]))
			{
				$vdevice = $vehicle[$x]->vehicle_device;
				$vid = $vehicle[$x]->vehicle_id;
				$vname = $vehicle[$x]->vehicle_name;
				$vno = $vehicle[$x]->vehicle_no; 
			}
			else
			{
				echo "FINISH!!";
	
			}
			if (isset($row_lastservice[$x]))
			{
				$last_vdevice = $row_lastservice[$x]->data_hm_vehicle_device;
				$last_vid = $row_lastservice[$x]->data_hm_vehicle_id;
				$last_vservice = date("Y-m-d 00:00:00", strtotime($row_lastservice[$x]->data_hm_last_service . " " . "00:00:00"));
				$last_vvalue = $row_lastservice[$x]->data_hm_last_service_value;
				//print_r($last_vservice);exit();
			}
			else
			{
				echo "FINISH!!";
	
			}
			
			$this->dbtransporter->order_by("history_trip_mileage_start_time","asc");
			
			$this->dbtransporter->where("history_trip_mileage_vehicle_id", $vdevice);
			$this->dbtransporter->where("history_trip_mileage_start_time >=",$last_vservice);
			$this->dbtransporter->where("history_trip_mileage_end_time <=", $enddate);
			$q = $this->dbtransporter->get("history_trip_mileage_ppi");
			$rows = $q->result();
			//print_r($rows);exit();
			
			$cummulative = 0;
			$tot_hour_daily = 0;
			$tot_dur_daily = 0;
			$string_cum = "";
			//daily
			if ((isset($rows)) && (count($rows)>0))
			{

				//printf("PROCESS NUMBER	 : %s \r\n", ++$i." of ".$totalv);
				//printf("VEHICLE : %s \r\n", $vdevice);
				
				for ($i=0;$i<count($rows);$i++)
				{
					$dur = $rows[$i]->history_trip_mileage_duration;
					$tot_dur_daily = $tot_dur_daily + $rows[$i]->history_trip_mileage_duration;
				
					$ex = explode(" ",$dur);
					if (isset($ex[1]) && ($ex[1] == "Min"))
					{
						$detik = $ex[0] * 60;
					}
					elseif (isset($ex[1]) && ($ex[1] == "Hour"))
					{
					
						$detik = $ex[0] * 60 * 60;
						if (isset($ex[2]))
						{
							$det = $ex[2] * 60;
							$detik  = $detik + $det;
						}
					}
					//satuan detik
					$tot_hour_daily = $tot_hour_daily + $detik; 
					
				}
			}
			
			//get data hourmeter
			$this->dbtransporter = $this->load->database("transporter",true);
			$this->dbtransporter->order_by("data_hm_vehicle_device", "desc");
			$this->dbtransporter->where("data_hm_vehicle_device", $vdevice);
			$q_hm = $this->dbtransporter->get("data_hm_ppi");
			$rows_hm = $q_hm->row();
			//print_r($rows_hm);exit();
			
			//if ($rows_hm->data_hm_datetime < $startdate)
		
				if ((isset($rows_hm)) && (count($rows_hm)>0)){
					$value_lastsvc = $rows_hm->data_hm_last_service_value * 3600;
				}else{
					$value_lastsvc = 0;
				}
				
				$cummulative = $tot_hour_daily + $value_lastsvc;
				//dari detik convert ke jam 
				$tot_hour_cum = $cummulative / 3600;
				$tot_hour_daily_cum = $tot_hour_daily / 3600;
				//print_r($tot_hour_daily_cum);exit();
				
										$value_hour = "";
										$value_min = "";
										
										$conval = $cummulative;
										$seconds = $conval;
										
										// extract hours
										$hours = floor($seconds / (60 * 60));
	 
										// extract minutes
										$divisor_for_minutes = $seconds % (60 * 60);
										$minutes = floor($divisor_for_minutes / 60);
	 
										// extract the remaining seconds
										$divisor_for_seconds = $divisor_for_minutes % 60;
										$seconds = ceil($divisor_for_seconds);
										
										if(isset($hours) && $hours > 0)
										{
											if($hours > 0 && $hours <= 1)
											{
												$value_hour = $hours." "."Hour"." ";
											}
											if($hours >= 2)
											{
												$value_hour = $hours." "."Hours"." ";
											}
										}
										if(isset($minutes) && $minutes > 0)
										{
											if($minutes > 0 && $minutes <= 1 )
											{
												$value_min = $minutes." "."Minute"." ";
											}
											if($minutes >= 2)
											{
												$value_min = $minutes." "."Minutes"." ";
											}
										}
										
									
					$string_cum = $value_hour." ".$value_min;	

			//get alert hourmeter
			$this->dbtransporter->order_by("hm_alert_vehicle_device", "desc");
			$this->dbtransporter->where("hm_alert_vehicle_device", $vdevice);
			$q_alert = $this->dbtransporter->get("data_hm_alert_ppi");
			$rows_alert = $q_alert->row();
			
			//get config hourmeter
			/* $this->dbtransporter->order_by("hm_config_vehicle_device", "desc");
			$this->dbtransporter->where("hm_config_vehicle_device", $vdevice);
			$q_config = $this->dbtransporter->get("data_hm_config_ppi");
			$rows_config = $q_config->row();
			$config = $rows_config->hm_config_value;
			$config_min = $rows_config->hm_config_value - 20; */
			$config = 250;
			$config_min = 230;
			
			//insert ke alert hourmeter
			unset($rows);
			$rows['hm_alert_vehicle_id'] = $vid;
			$rows['hm_alert_vehicle_name'] = $vname;
			$rows['hm_alert_vehicle_no'] = $vno;
			$rows['hm_alert_vehicle_device'] = $vdevice;
            $rows['hm_alert_datetime'] = $enddate;
			$rows['hm_alert_value'] = $tot_hour_cum;
			$rows['hm_alert_string'] = $string_cum;
			
			if ($tot_hour_daily_cum > $config_min && $tot_hour_daily_cum < $config)
			{
				$rows['hm_alert_status'] = 1;
			}
			if ($tot_hour_daily_cum > $config)
			{
				$rows['hm_alert_status'] = 2;
			}
			
			//insert / update
			if ((isset($rows_alert)) && (count($rows_alert)>0))
			{
				
				//printf("UPDATE ALERT HOURMETER : %s \r\n", $vdevice); 
				$this->dbtransporter->where("hm_alert_vehicle_id", $vid);
				$this->dbtransporter->update("data_hm_alert_ppi",$rows);
			}
			else
			{
				//printf("INSERT ALERT HOURMETER  : %s \r\n", $vdevice); 
				$this->dbtransporter->insert("data_hm_alert_ppi",$rows);
			}
				
			$this->dbtransporter->close();
			
			//printf("FINISH 	: %s \r\n", $vdevice);
			//printf("================================= \r\n");
		}
		
			
		//printf("DONE !! \r\n");
			

	}
	
	function get_stnk_alert()
	{
		$this->dbtransporter = $this->load->database('transporter', true);
		$end_stnk_expired = date("Y-m-d",strtotime("+14 days"));
		
		//expired
		$this->dbtransporter->select("count(*) as total");
		$this->dbtransporter->where("mobil_company", 340);
		$this->dbtransporter->where("mobil_stnk_expired <", date("Y-m-d"));
		
		$qt1 = $this->dbtransporter->get("mobil");
		$rt1 = $qt1->row();
		$total1 = $rt1->total;
		
		//will expired ( 2 minggu )
		$this->dbtransporter->select("count(*) as total");
		$this->dbtransporter->where("mobil_company", 340);
		$this->dbtransporter->where("mobil_stnk_expired >=", date("Y-m-d"));
		$this->dbtransporter->where("mobil_stnk_expired <=", $end_stnk_expired);
		
		$qt2 = $this->dbtransporter->get("mobil");
		$rt2 = $qt2->row();
		$total2 = $rt2->total;
		
		$total = $total1 + $total2;
		
		if ($total > 0)
		{
			$html = '<b><a href="'.base_url().'transporter/ppi_mod_vehicle_maintenance/stnk_expired/">STNK ('.$total.')</a></b>';
		}
		else
		{
			$html = '<b><a href="'.base_url().'transporter/ppi_mod_vehicle_maintenance/stnk_expired/">STNK </a></b>';
		}
						
		echo json_encode(array("total"=>$total, "notification"=>$html));
	}
	
	function get_kir_alert()
	{
		$this->dbtransporter = $this->load->database('transporter', true);
		$end_kir_expired = date("Y-m-d",strtotime("+14 days"));
		
		//expired
		$this->dbtransporter->select("count(*) as total");
		$this->dbtransporter->where("mobil_company", 340);
		$this->dbtransporter->where("mobil_kir_active_date <", date("Y-m-d"));
		
		$qt1 = $this->dbtransporter->get("mobil");
		$rt1 = $qt1->row();
		$total1 = $rt1->total;
		
		//will expired ( 2 minggu )
		$this->dbtransporter->select("count(*) as total");
		$this->dbtransporter->where("mobil_company", 340);
		$this->dbtransporter->where("mobil_kir_active_date >=", date("Y-m-d"));
		$this->dbtransporter->where("mobil_kir_active_date <=", $end_kir_expired);
		
		$qt2 = $this->dbtransporter->get("mobil");
		$rt2 = $qt2->row();
		$total2 = $rt2->total;
		
		$total = $total1 + $total2;
		
		if ($total > 0)
		{
			$html = '<b><a href="'.base_url().'transporter/ppi_mod_vehicle_maintenance/kir_expired/"> KIR ('.$total.')</a></b>';
		}
		else
		{
			$html = '<b><a href="'.base_url().'transporter/ppi_mod_vehicle_maintenance/kir_expired/"> KIR </a></b>';
		}
						
		echo json_encode(array("total"=>$total, "notification"=>$html));
	}
	
	function get_hm_alert()
	{
		$this->dbtransporter = $this->load->database('transporter', true);
		
		$this->dbtransporter->select("count(*) as total");
		$this->dbtransporter->where("hm_alert_status >=", 1);
		
		$qt = $this->dbtransporter->get("data_hm_alert_ppi");
		$rt = $qt->row();
		$total = $rt->total;
		
		if ($total > 0)
		{
			$html = '<b><a href="'.base_url().'transporter/ppi_hourmeter/alert/"> HM ('.$total.')</a></b>';
		}
		else
		{
			$html = '<b><a href="'.base_url().'transporter/ppi_hourmeter/alert/"> HM </a></b>';
		}
						
		echo json_encode(array("total"=>$total, "notification"=>$html));
	}
	
	function dateDiff($time1, $time2, $precision = 6) {
			// If not numeric then convert texts to unix timestamps
			if (!is_int($time1)) {
			  $time1 = strtotime($time1);
			}
			if (!is_int($time2)) {
			  $time2 = strtotime($time2);
			}

			// If time1 is bigger than time2
			// Then swap time1 and time2
			if ($time1 > $time2) {
			  $ttime = $time1;
			  $time1 = $time2;
			  $time2 = $ttime;
			}

			// Set up intervals and diffs arrays
			$intervals = array('year','month','day','hour','minute','second');
			$diffs = array();

			// Loop thru all intervals
			foreach ($intervals as $interval) {
			  // Set default diff to 0
			  $diffs[$interval] = 0;
			  // Create temp time from time1 and interval
			  $ttime = strtotime("+1 " . $interval, $time1);
			  // Loop until temp time is smaller than time2
			  while ($time2 >= $ttime) {
				$time1 = $ttime;
				$diffs[$interval]++;
				// Create new temp time from time1 and interval
				$ttime = strtotime("+1 " . $interval, $time1);
			  }
			}

			$count = 0;
			$times = array();
			// Loop thru all diffs
			foreach ($diffs as $interval => $value) {
			  // Break if we have needed precission
			  if ($count >= $precision) {
				break;
			  }
			  // Add value and interval 
			  // if value is bigger than 0
			  if ($value > 0) {
				// Add s if value is not 1
				if ($value != 1) {
				 $interval .= "s";
				}
				// Add value and interval to times array
				$times[] = $value . " " . $interval;
				$count++;
				}
			}

			// Return string with times
			return implode(", ", $times);
		  }
		  
		  
}
