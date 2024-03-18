<?php
include "base.php";
class Andalas_job_schedule extends Base {

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
		
		$this->params['sortby'] = "job_id";
		$this->params['orderby'] = "desc";
		$this->params['title'] = "Job Schedule List";
				
		$this->params["content"] =  $this->load->view('job/job_list', $this->params, true);	
		$this->load->view("templatesess", $this->params);	
	}
	
	function search_job(){
	
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
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "job_id";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "desc";
		$startdate = isset($_POST['startdate']) ? $_POST['startdate'] : "";
		$starttime = isset($_POST['starttime']) ? $_POST['starttime'] : "";
		$scheduledate = date("Y-m-d H:i:s", strtotime($startdate . " " . $starttime));
		$jobnumber = isset($_POST['jobnumber']) ? $_POST['jobnumber'] : "";
		
		$mobil_no = isset($_POST['mobil_no']) ? $_POST['mobil_no'] : "";
		$mobil_id = isset($_POST['mobil_id']) ? $_POST['mobil_id'] : 0;
		$mobil_device = isset($_POST['mobil_device']) ? $_POST['mobil_device'] : "";
		$mobil_name = isset($_POST['mobil_name']) ? $_POST['mobil_name'] : "";
		$driver = isset($_POST['driver']) ? $_POST['driver'] : "";
		
		$nopo = isset($_POST['nopo']) ? $_POST['nopo'] : "";
		$dimensi_p = isset($_POST['dimensi_p']) ? $_POST['dimensi_p'] : 0;
		$dimensi_l = isset($_POST['dimensi_l']) ? $_POST['dimensi_l'] : 0;
		$dimensi_t = isset($_POST['dimensi_t']) ? $_POST['dimensi_t'] : 0;
		$weight = isset($_POST['weight']) ? $_POST['weight'] : "";
		$to = isset($_POST['to']) ? $_POST['to'] : "";
		$from = isset($_POST['from']) ? $_POST['from'] : "";
		
		$area = isset($_POST['area']) ? $_POST['area'] : "";
		$customer_company = isset($_POST['customer_company']) ? $_POST['customer_company'] : "";
		$enddate = isset($_POST['enddate']) ? $_POST['enddate'] : "";
		$status = isset($_POST['status']) ? $_POST['status'] : "";
		$searchdate = isset($_POST['searchdate']) ? $_POST['searchdate'] : "all";
		
		//Get vehicle
		$row_mobil = $this->get_mobil();
		$this->params["rmobil"] = $row_mobil;
		
		//get driver
		$rows_driver = $this->get_driver();
		$this->params["rdriver"] = $rows_driver;
		
		//get customer
		$rows_customer_company = $this->get_customer_company();
		$this->params["rcustomercompany"] = $rows_customer_company;
		
		$this->dbtransporter->order_by("job_id", "desc");
		$this->dbtransporter->where("job_flag", 0);
		$this->dbtransporter->where("job_company", $user_company);
		if($searchdate != "all"){
				if($startdate != ""){
						$this->dbtransporter->where("job_date >=", $startdate);
					}
					if($enddate != ""){
						$this->dbtransporter->where("job_date <=", $enddate);
					}
			}
			
		switch($field)
		{
			case "job_number":
				$this->dbtransporter->where("job_number LIKE '%".$keyword."%'", null);				
			break;			
			case "job_po":
				$this->dbtransporter->where("job_po LIKE '%".$keyword."%'", null);				
			break;		
			case "job_vehicle_no":
				$this->dbtransporter->where("job_vehicle_no LIKE '%".$keyword."%'", null);				
			break;	
			case "job_status":
				$this->dbcar->where("job_status", $status);				
			break;			
	
		}
		$this->dbtransporter->join("driver", "driver_id = job_driver", "left");
		$this->dbtransporter->join("mobil", "mobil_id = job_mobil_id", "left");
		$this->dbtransporter->join("andalas_customer_company", "customer_company_id = job_customer_company", "left");
		$q = $this->dbtransporter->get("andalas_job", $this->config->item("limit_records_job"), $offset);
		$rows = $q->result();
		//print_r($rows);exit();

		//hitung total
		$this->dbtransporter->select("count(*) as total");
		
		$this->dbtransporter->order_by("job_id", "desc");
		$this->dbtransporter->where("job_flag", 0);
		$this->dbtransporter->where("job_company", $user_company);
		if($searchdate != "all"){
				if($startdate != ""){
						$this->dbtransporter->where("job_date >=", $startdate);
					}
					if($enddate != ""){
						$this->dbtransporter->where("job_date <=", $enddate);
					}
			}
			
		switch($field)
		{
			case "job_number":
				$this->dbtransporter->where("job_number LIKE '%".$keyword."%'", null);				
			break;			
			case "job_po":
				$this->dbtransporter->where("job_po LIKE '%".$keyword."%'", null);				
			break;		
			case "job_vehicle_no":
				$this->dbtransporter->where("job_vehicle_no LIKE '%".$keyword."%'", null);				
			break;	
			case "job_status":
				$this->dbcar->where("job_status", $status);				
			break;				
	
		}
		
		$this->dbtransporter->join("driver", "driver_id = job_driver", "left");
		$this->dbtransporter->join("mobil", "mobil_id = job_mobil_id", "left");
		$this->dbtransporter->join("andalas_customer_company", "customer_company_id = job_customer_company", "left");
		$qt = $this->dbtransporter->get("andalas_job");
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
	
		$html = $this->load->view('job/job_list_result', $this->params, true);
		
		$callback['html'] = $html;
		$callback['total'] = $total;
				
		echo json_encode($callback);
	}
	
	function add_job()
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
		
		//get vehicle
		$rows_mobil = $this->get_mobil();
		$this->params["rmobil"] = $rows_mobil;
		//get driver
		$rows_driver = $this->get_driver();
		$this->params["rdriver"] = $rows_driver;
		//get customer
		$rows_customer_company = $this->get_customer_company();
		$this->params["rcustomercompany"] = $rows_customer_company;
	
		$this->params["content"] = $this->load->view('job/job_add', $this->params, true);
        $this->load->view("templatesess", $this->params);
	
    }
	
	function save_job()
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
		
		$id = isset($_POST['id']) ? $_POST['id'] : 0;	
		$job = isset($_POST['job']) ? $_POST['job'] : "";
		
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "job_id";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "desc";
		$startdate = isset($_POST['startdate']) ? $_POST['startdate'] : "";
		$starttime = isset($_POST['starttime']) ? $_POST['starttime'] : "";
		$scheduledate = date("Y-m-d H:i:s", strtotime($startdate . " " . $starttime));
		$jobnumber = isset($_POST['jobnumber']) ? $_POST['jobnumber'] : "";
		$company = isset($_POST['company']) ? $_POST['company'] : 0;
		
		$mobil_no = isset($_POST['mobil_no']) ? $_POST['mobil_no'] : "";
		$mobil_id = isset($_POST['mobil_id']) ? $_POST['mobil_id'] : 0;
		$mobil_device = isset($_POST['mobil_device']) ? $_POST['mobil_device'] : "";
		$mobil_name = isset($_POST['mobil_name']) ? $_POST['mobil_name'] : "";
		$driver = isset($_POST['driver']) ? $_POST['driver'] : "";
		
		$nopo = isset($_POST['nopo']) ? $_POST['nopo'] : "";
		$dimensi_p = isset($_POST['dimensi_p']) ? $_POST['dimensi_p'] : 0;
		$dimensi_l = isset($_POST['dimensi_l']) ? $_POST['dimensi_l'] : 0;
		$dimensi_t = isset($_POST['dimensi_t']) ? $_POST['dimensi_t'] : 0;
		$weight = isset($_POST['weight']) ? $_POST['weight'] : "";
		$to = isset($_POST['to']) ? $_POST['to'] : "";
		$from = isset($_POST['from']) ? $_POST['from'] : "";
		$items = isset($_POST['items']) ? $_POST['items'] : "";
		
		$area = isset($_POST['area']) ? $_POST['area'] : "";
		$customer_company = isset($_POST['customer_company']) ? $_POST['customer_company'] : "";
		$enddate = isset($_POST['enddate']) ? $_POST['enddate'] : "";
		$status = isset($_POST['status']) ? $_POST['status'] : "";
		$notes = isset($_POST['notes']) ? $_POST['notes'] : "";
	
		$error = "";
		if ($job == "")
		{
			$error .= "- Please Select Job Number \n";	
		}else{
			
			$this->dbtransporter->where("job_number", $job);
			$this->dbtransporter->where("job_flag", 0);
			$qj = $this->dbtransporter->get("andalas_job");
			if($qj->num_rows() > 0 && $id== 0){
				$error .= "- Job Number already exist \n";
			
			}
		}
		if ($startdate == "")
		{
			$error .= "- Please Select Start Date \n";	
		}
		/* if ($enddate == "")
		{
			$error .= "- Please Select End Date \n";	
		} */
		if ($nopo == "")
		{
			$error .= "- Please Input No. PO \n";	
		}
		if ($items == "")
		{
			$error .= "- Please Input Items \n";	
		}
		if ($mobil_id == "")
		{
			$error .= "- Your Vehicle ID is null \n";	
		}
		if ($driver == "")
		{
			$error .= "- Please Input Driver \n";	
		}
		if ($customer_company == "")
		{
			$error .= "- Please Select Customer \n";	
		}
		/* if ($area == "")
		{
			$error .= "- Please Select Area \n";	
		} */
		
		if ($error != "")
		{
			$callback['error'] = true;	
			$callback['message'] = $error;	
			
			echo json_encode($callback);			
			return;
		}
		
			$this->dbtransporter->where("mobil_status", 1);
			$this->dbtransporter->where("mobil_id", $mobil_id);
			$qv = $this->dbtransporter->get("mobil");
			$rmobil = $qv->row();
			$this->dbtransporter->close();
			
		unset($data);
			
            $data['job_mobil_id'] = $mobil_id;
			$data['job_mobil_name'] = $rmobil->mobil_name;
            $data['job_mobil_device'] = $rmobil->mobil_device;
			$data['job_mobil_no'] = $rmobil->mobil_no;
			$data['job_driver'] = $driver;
			$data['job_number'] = $job;
			$data['job_po'] = $nopo;
            $data['job_dimensi_p'] = $dimensi_p;
			$data['job_dimensi_l'] = $dimensi_l;
			$data['job_dimensi_t'] = $dimensi_t;
			$data['job_weight'] = $weight;
			$data['job_date'] = $startdate;
			$data['job_time'] = $starttime;
			$data['job_sch_date'] = $scheduledate;
			$data['job_from'] = $from;
			$data['job_to'] = $to;
			$data['job_area'] = $area;
			$data['job_customer_company'] = $customer_company;
			$data['job_company'] = $company;
			$data['job_items'] = $items;
			$data['job_notes'] = $notes;
			
            $this->dbtransporter->insert("andalas_job", $data);
			
			$callback['error'] = false;	
			$callback['message'] = "Add New Job Successfully Submitted";
			$callback["redirect"]= base_url()."andalas_job_schedule";
			echo json_encode($callback);
			return;
	}
	
	function edit_job(){
	
		if ($this->sess->user_type != 1){
			if (!$this->sess->user_company){
			redirect(base_url());
			}
		}
		
		$user_company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		$user_group = $this->sess->user_group;
		
		$id = $this->uri->segment(3);
		$this->dbtransporter=$this->load->database("transporter", TRUE);
		
		//get vehicle
		$rows_mobil = $this->get_mobil();
		$this->params["rmobil"] = $rows_mobil;
		
		//get driver
		$rows_driver = $this->get_driver();
		$this->params["rdriver"] = $rows_driver;
		
		//get customer
		$rows_customer_company = $this->get_customer_company();
		$this->params["rcustomercompany"] = $rows_customer_company;
		
		
		if ($id) {
		    $this->dbtransporter->where("job_id", $id);
            $q = $this->dbtransporter->get("andalas_job");
			
		
			if ($q->num_rows == 0) { return; }
				$row = $q->row();
				//print_r($row);exit();
				$this->params['row'] = $row;
				$this->params['title'] = "Edit Job File";
				$this->params['content'] = $this->load->view("job/job_edit", $this->params, true);
				$this->load->view("templatesess", $this->params);
				$this->dbtransporter->close();
		} 
		else 
		{
			$this->dbtransporter->close();
			redirect(base_url()."andalas_job_schedule");
		}
	}
	
	function update_job(){
	
		if ($this->sess->user_type != 1){
			if (!$this->sess->user_company){
			redirect(base_url());
			}
		}
		
		$user_company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		$user_group = $this->sess->user_group;
		
		$this->dbtransporter=$this->load->database("transporter", TRUE);
		
		$id = isset($_POST['id']) ? $_POST['id'] : 0;	
		$job = isset($_POST['job']) ? $_POST['job'] : "";
		
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "job_id";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "desc";
		$startdate = isset($_POST['startdate']) ? $_POST['startdate'] : "";
		$starttime = isset($_POST['starttime']) ? $_POST['starttime'] : "";
		$scheduledate = date("Y-m-d H:i:s", strtotime($startdate . " " . $starttime));
		$jobnumber = isset($_POST['jobnumber']) ? $_POST['jobnumber'] : "";
		
		$mobil_no = isset($_POST['mobil_no']) ? $_POST['mobil_no'] : "";
		$mobil_id = isset($_POST['mobil_id']) ? $_POST['mobil_id'] : 0;
		$mobil_device = isset($_POST['mobil_device']) ? $_POST['mobil_device'] : "";
		$mobil_name = isset($_POST['mobil_name']) ? $_POST['mobil_name'] : "";
		$driver = isset($_POST['driver']) ? $_POST['driver'] : "";
		
		$nopo = isset($_POST['nopo']) ? $_POST['nopo'] : "";
		$dimensi_p = isset($_POST['dimensi_p']) ? $_POST['dimensi_p'] : 0;
		$dimensi_l = isset($_POST['dimensi_l']) ? $_POST['dimensi_l'] : 0;
		$dimensi_t = isset($_POST['dimensi_t']) ? $_POST['dimensi_t'] : 0;
		$weight = isset($_POST['weight']) ? $_POST['weight'] : "";
		$to = isset($_POST['to']) ? $_POST['to'] : "";
		$from = isset($_POST['from']) ? $_POST['from'] : "";
		$items = isset($_POST['items']) ? $_POST['items'] : "";
		
		$area = isset($_POST['area']) ? $_POST['area'] : "";
		$customer_company = isset($_POST['customer_company']) ? $_POST['customer_company'] : "";
		$enddate = isset($_POST['enddate']) ? $_POST['enddate'] : "";
		$status = isset($_POST['status']) ? $_POST['status'] : "";
		$notes = isset($_POST['notes']) ? $_POST['notes'] : "";
		
		$error = "";
		if ($job == "")
		{
			$error .= "- Please Select Job Number \n";	
		}else{
			
			$this->dbtransporter->where("job_number", $job);
			$this->dbtransporter->where("job_flag", 0);
			$qj = $this->dbtransporter->get("andalas_job");
			if($qj->num_rows() > 0 && $id== 0){
				$error .= "- Job Number already exist \n";
			
			}
		}
		if ($startdate == "")
		{
			$error .= "- Please Select Start Date \n";	
		}
		/* if ($enddate == "")
		{
			$error .= "- Please Select End Date \n";	
		} */
		if ($nopo == "")
		{
			$error .= "- Please Input No. PO \n";	
		}
		if ($items == "")
		{
			$error .= "- Please Input Items \n";	
		}
		if ($mobil_id == "")
		{
			$error .= "- Your Vehicle ID is null \n";	
		}
		if ($driver == "")
		{
			$error .= "- Please Input Driver \n";	
		}
		if ($customer_company == "")
		{
			$error .= "- Please Select Customer \n";	
		}
		/* if ($area == "")
		{
			$error .= "- Please Select Area \n";	
		} */
		
		if ($error != "")
		{
			$callback['error'] = true;	
			$callback['message'] = $error;	
			
			echo json_encode($callback);			
			return;
		}
		
			$this->dbtransporter->where("mobil_status", 1);
			$this->dbtransporter->where("mobil_id", $mobil_id);
			$qv = $this->dbtransporter->get("mobil");
			$rmobil = $qv->row();
			$this->dbtransporter->close();
			
		unset($data);
		
			$data['job_mobil_id'] = $mobil_id;
			$data['job_mobil_name'] = $rmobil->mobil_name;
            $data['job_mobil_device'] = $rmobil->mobil_device;
			$data['job_mobil_no'] = $rmobil->mobil_no;
			$data['job_driver'] = $driver;
			$data['job_number'] = $job;
			$data['job_po'] = $nopo;
            $data['job_dimensi_p'] = $dimensi_p;
			$data['job_dimensi_l'] = $dimensi_l;
			$data['job_dimensi_t'] = $dimensi_t;
			$data['job_weight'] = $weight;
			$data['job_date'] = $startdate;
			$data['job_time'] = $starttime;
			$data['job_sch_date'] = $scheduledate;
			$data['job_from'] = $from;
			$data['job_to'] = $to;
			$data['job_area'] = $area;
			$data['job_customer_company'] = $customer_company;
			$data['job_items'] = $items;
			$data['job_notes'] = $notes;
			
			$this->dbtransporter->where("job_id", $id);
            $this->dbtransporter->update("andalas_job", $data);
			$this->dbtransporter->close();
		
			$callback['error'] = false;
			$callback['message'] = "Job File Successfully Updated";
			$callback['redirect'] = base_url()."andalas_job_schedule";
			echo json_encode($callback);
			return;
	}
	
	function export_to_excel()
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
		
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "job_id";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "desc";
		$startdate = isset($_POST['startdate']) ? $_POST['startdate'] : "";
		$starttime = isset($_POST['starttime']) ? $_POST['starttime'] : "";
		$datetime = date("Y-m-d H:i:s", strtotime($startdate . " " . $starttime));
		$job = isset($_POST['job']) ? $_POST['job'] : "";
		$nopo = isset($_POST['nopo']) ? $_POST['nopo'] : "";
		$vehicle_no = isset($_POST['vehicle_no']) ? $_POST['vehicle_no'] : "";
		$vehicle_id = isset($_POST['vehicle_id']) ? $_POST['vehicle_id'] : "";
		$vehicle = isset($_POST['vehicle']) ? $_POST['vehicle'] : 0;
		$vehicle_device = isset($_POST['vehicle_device']) ? $_POST['vehicle_device'] : "";
		$vehicle_name = isset($_POST['vehicle_name']) ? $_POST['vehicle_name'] : "";
		$dimensi_p = isset($_POST['dimensi_p']) ? $_POST['dimensi_p'] : 0;
		$dimensi_l = isset($_POST['dimensi_l']) ? $_POST['dimensi_l'] : 0;
		$dimensi_t = isset($_POST['dimensi_t']) ? $_POST['dimensi_t'] : 0;
		$weight = isset($_POST['weight']) ? $_POST['weight'] : "";
		$to = isset($_POST['to']) ? $_POST['to'] : "";
		$from = isset($_POST['from']) ? $_POST['from'] : "";
		$driver = isset($_POST['driver']) ? $_POST['driver'] : "";
		$area = isset($_POST['area']) ? $_POST['area'] : "";
		$customer = isset($_POST['customer']) ? $_POST['customer'] : "";
		$enddate = isset($_POST['enddate']) ? $_POST['enddate'] : "";
		$status = isset($_POST['status']) ? $_POST['status'] : "";
		$searchdate = isset($_POST['searchdate']) ? $_POST['searchdate'] : "all";
		
		//Get vehicle
		$row_vehicle = $this->get_mobil();
		$this->params["vehicle"] = $row_vehicle;
		
		//get driver
		$rows_driver = $this->get_driver();
		$this->params["rdriver"] = $rows_driver;
		
		$this->dbtransporter->order_by("job_id", "desc");
		$this->dbtransporter->where("job_flag", 0);
		
		if($searchdate != "all"){
				if($startdate != ""){
						$this->dbtransporter->where("job_date >=", $startdate);
					}
					if($enddate != ""){
						$this->dbtransporter->where("job_date <=", $enddate);
					}
			}
			
		switch($field)
		{
			case "job_number":
				$this->dbtransporter->where("job_number LIKE '%".$keyword."%'", null);				
			break;			
			case "job_po":
				$this->dbtransporter->where("job_po LIKE '%".$keyword."%'", null);				
			break;		
			case "job_vehicle_no":
				$this->dbtransporter->where("job_vehicle_no LIKE '%".$keyword."%'", null);				
			break;	
			case "job_status":
				$this->dbcar->where("job_status", $status);				
			break;			
	
		}
		
		$this->dbtransporter->join("driver", "driver_id = job_driver", "left");
		$q = $this->dbtransporter->get("andalas_job");
		$rows = $q->result();
		//print_r($rows);exit();

		//hitung total
		$this->dbtransporter->select("count(*) as total");
		
		$this->dbtransporter->order_by("job_id", "desc");
		$this->dbtransporter->where("job_flag", 0);
		
		if($searchdate != "all"){
				if($startdate != ""){
						$this->dbtransporter->where("job_date >=", $startdate);
					}
					if($enddate != ""){
						$this->dbtransporter->where("job_date <=", $enddate);
					}
			}
			
		switch($field)
		{
			case "job_number":
				$this->dbtransporter->where("job_number LIKE '%".$keyword."%'", null);				
			break;			
			case "job_po":
				$this->dbtransporter->where("job_po LIKE '%".$keyword."%'", null);				
			break;		
			case "job_vehicle_no":
				$this->dbtransporter->where("job_vehicle_no LIKE '%".$keyword."%'", null);				
			break;	
			case "job_status":
				$this->dbcar->where("job_status", $status);				
			break;				
	
		}
		
		$qt = $this->dbtransporter->get("andalas_job");
		$rt = $qt->row();
		$total = $rt->total;
		
		/** PHPExcel */
		include 'class/PHPExcel.php';
			
		/** PHPExcel_Writer_Excel2007 */
		include 'class/PHPExcel/Writer/Excel2007.php';
			
		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();
		
		// Set properties
		$objPHPExcel->getProperties()->setCreator("transporter.lacak-mobil.com");
		$objPHPExcel->getProperties()->setLastModifiedBy("transporter.lacak-mobil.com");
		$objPHPExcel->getProperties()->setTitle("Job File Report");
		$objPHPExcel->getProperties()->setSubject("Job File Report");
		$objPHPExcel->getProperties()->setDescription("Job File Report");
		
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
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);			
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
		//$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
		//Header
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->mergeCells('A1:M1');
		$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'JOB FILE REPORT');
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		
		
		if($startdate || $enddate){
			
			$objPHPExcel->getActiveSheet()->SetCellValue('B3', 'Date :');
			$objPHPExcel->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->SetCellValue('C3', date("d-m-Y",strtotime($startdate))." "."to"." ".date("d-m-Y",strtotime($enddate)));
		}
		
	
		//tambahan costs
		
		$objPHPExcel->getActiveSheet()->SetCellValue('B5', 'Total Record: ');
		$objPHPExcel->getActiveSheet()->getStyle('B5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('B5')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->SetCellValue('C5', $total);
		$objPHPExcel->getActiveSheet()->getStyle('C5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$objPHPExcel->getActiveSheet()->getStyle('C5')->getFont()->setBold(true);
		
		/* $objPHPExcel->getActiveSheet()->SetCellValue('D5', 'Total Order : ');
		$objPHPExcel->getActiveSheet()->getStyle('D5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('D5')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->SetCellValue('E5', $total);
		$objPHPExcel->getActiveSheet()->getStyle('E5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$objPHPExcel->getActiveSheet()->getStyle('E5')->getFont()->setBold(true); */
		
		//Top Header
		$objPHPExcel->getActiveSheet()->SetCellValue('A7', 'No');
		$objPHPExcel->getActiveSheet()->SetCellValue('B7', 'Job Number');
		$objPHPExcel->getActiveSheet()->SetCellValue('C7', 'No PO');
		$objPHPExcel->getActiveSheet()->SetCellValue('D7', 'Vehicle No');
		$objPHPExcel->getActiveSheet()->SetCellValue('E7', 'Vehicle Name');
		$objPHPExcel->getActiveSheet()->SetCellValue('F7', 'Driver');
		$objPHPExcel->getActiveSheet()->SetCellValue('G7', 'Area');
		$objPHPExcel->getActiveSheet()->SetCellValue('H7', 'Datetime');
		$objPHPExcel->getActiveSheet()->SetCellValue('I7', 'Start From');
		$objPHPExcel->getActiveSheet()->SetCellValue('J7', 'Destination');
		$objPHPExcel->getActiveSheet()->SetCellValue('K7', 'customer');
		$objPHPExcel->getActiveSheet()->SetCellValue('L7', 'Dimension');
		$objPHPExcel->getActiveSheet()->SetCellValue('M7', 'Status');
		//$objPHPExcel->getActiveSheet()->SetCellValue('N7', 'Notes');
		
		
		$objPHPExcel->getActiveSheet()->getStyle('A7:M7')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A7:M7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		$i = 1;
		for ($j=0;$j<count($rows);$j++)
		{
			$objPHPExcel->getActiveSheet()->SetCellValue('A'.(7+$i), $i);
			$objPHPExcel->getActiveSheet()->getStyle('A'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$objPHPExcel->getActiveSheet()->SetCellValue('B'.(7+$i), $rows[$j]->job_number);
			$objPHPExcel->getActiveSheet()->getStyle('B'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$objPHPExcel->getActiveSheet()->SetCellValue('C'.(7+$i), $rows[$j]->job_po);
			$objPHPExcel->getActiveSheet()->getStyle('C'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$objPHPExcel->getActiveSheet()->SetCellValue('D'.(7+$i), $rows[$j]->job_vehicle_no);
			$objPHPExcel->getActiveSheet()->getStyle('D'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$objPHPExcel->getActiveSheet()->SetCellValue('E'.(7+$i), $rows[$j]->job_vehicle_name);
			$objPHPExcel->getActiveSheet()->getStyle('E'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$objPHPExcel->getActiveSheet()->SetCellValue('F'.(7+$i), $rows[$j]->driver_name);
			$objPHPExcel->getActiveSheet()->getStyle('F'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$objPHPExcel->getActiveSheet()->SetCellValue('G'.(7+$i), $rows[$j]->job_area);
			$objPHPExcel->getActiveSheet()->getStyle('G'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$objPHPExcel->getActiveSheet()->SetCellValue('H'.(7+$i), date("d-m-Y H:i",strtotime($rows[$j]->job_date." ".$rows[$j]->job_time)));
			$objPHPExcel->getActiveSheet()->getStyle('H'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$objPHPExcel->getActiveSheet()->SetCellValue('I'.(7+$i), $rows[$j]->job_from);
			$objPHPExcel->getActiveSheet()->getStyle('I'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$objPHPExcel->getActiveSheet()->SetCellValue('J'.(7+$i), $rows[$j]->job_to);
			$objPHPExcel->getActiveSheet()->getStyle('J'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$objPHPExcel->getActiveSheet()->SetCellValue('K'.(7+$i), $rows[$j]->job_customer);
			$objPHPExcel->getActiveSheet()->getStyle('K'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$objPHPExcel->getActiveSheet()->SetCellValue('L'.(7+$i), $rows[$j]->job_dimensi_p." cm x "." ".$rows[$j]->job_dimensi_l." cm x "." ".$rows[$j]->job_dimensi_t." cm ");
			$objPHPExcel->getActiveSheet()->getStyle('L'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			//status
			if (isset($rows[$j]->job_status))
			{
				
				if ($rows[$j]->job_status == 1)
				{
					$status = "On Going";
					
				}
				if ($rows[$j]->job_status == 2)
				{
					$status = "Delivered";
					
				}
				
			$objPHPExcel->getActiveSheet()->SetCellValue('M'.(7+$i), $status);
			$objPHPExcel->getActiveSheet()->getStyle('M'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			}
			
			$i++;
		}
		
		$styleArray = array(
				  'borders' => array(
				    'allborders' => array(
				      'style' => PHPExcel_Style_Border::BORDER_THIN
				    )
				  )
				);
			
			$objPHPExcel->getActiveSheet()->getStyle('A7:M'.(6+$i))->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getStyle('A7:M'.(6+$i))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
			$objPHPExcel->getActiveSheet()->getStyle('A7:M'.(6+$i))->getAlignment()->setWrapText(true);
			
			// Rename sheet
			$objPHPExcel->getActiveSheet()->setTitle('JOB FILE REPORT');
			 
			// Save Excel
			$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
			@mkdir(REPORT_PATH, DIR_WRITE_MODE);
			
			if($startdate || $enddate){
				$filedate = date("Ymd",strtotime($startdate))."_".date("Ymd",strtotime($enddate));
			}
			else{
				$filedate = date("Ymd");
			}
			$filecreatedname = "job_(".$filedate.")".".xls";
			
			$objWriter->save(REPORT_PATH.$filecreatedname);
		
			$output = '{"success":true,"errMsg":"","filename":"' .base_url() . "assets/media/report/" . $filecreatedname.'"}';
			echo $output;
			return;
		
		
	}
	
	function delete_job($id)
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
		
		$data["job_flag"] = 1;		
		$this->dbtransporter->where("job_id", $id);
		if($this->dbtransporter->update("andalas_job", $data)){
			$callback['message'] = "Data has been deleted";
			$callback['error'] = false;	
		}else{
			$callback['message'] = "Failed delete data";
			$callback['error'] = true;	
		}
		
		echo json_encode($callback);
	}
	
	function get_mobil(){
	
		if ($this->sess->user_type != 1){
			if (!$this->sess->user_company){
			redirect(base_url());
			}
		}
		
		$user_company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		$user_group = $this->sess->user_group;
		
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		
		$this->dbtransporter->order_by("mobil_name", "asc");
		$this->dbtransporter->where("mobil_company", $user_company);
		$this->dbtransporter->where("mobil_status", 1);
		$q = $this->dbtransporter->get("mobil");
		$row_mobil = $q->result();
        
		$data_mobil = $row_mobil;
		//print_r($data_vehicle);exit();
        $this->dbtransporter->cache_delete_all();
        return $data_mobil;
	}
	
	function get_driver(){
		
		if ($this->sess->user_type != 1){
			if (!$this->sess->user_company){
			redirect(base_url());
			}
		}
		
		$user_company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		$user_group = $this->sess->user_group;
		
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		
		$this->dbtransporter->order_by("driver_name", "asc");
		$this->dbtransporter->where("driver_company", $user_company);
		$this->dbtransporter->where("driver_status <>", 2);
		$q = $this->dbtransporter->get("driver");
		$row_driver = $q->result();
        
		$data_driver = $row_driver;
	
        $this->dbtransporter->cache_delete_all();
        return $data_driver;
	}
	
	function get_customer_company(){
	
		if ($this->sess->user_type != 1){
			if (!$this->sess->user_company){
			redirect(base_url());
			}
		}
		
		$user_company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		$user_group = $this->sess->user_group;
		
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		
		$this->dbtransporter->order_by("customer_company_name", "asc");
		$this->dbtransporter->where("customer_company_usercompany", $user_company);
		$this->dbtransporter->where("customer_company_status", 1);
		$this->dbtransporter->where("customer_company_flag", 0);
		
		//$this->dbtransporter->join("andalas_customer_company", "customer_company_id = customer_company_group", "left");
		$q = $this->dbtransporter->get("andalas_customer_company");
		$row_customer_company = $q->result();
        
		$data_customer_company = $row_customer_company;
        $this->dbtransporter->cache_delete_all();
        return $data_customer_company;
	}
	
	function set_delivered()
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
		$this->dbtransporter=$this->load->database("transporter", TRUE);
		
		$this->dbtransporter->where("job_id", $id);
		$this->dbtransporter->join("driver", "driver_id = job_driver", "left");
		$this->dbtransporter->join("mobil", "mobil_id = job_mobil_id", "left");
		$this->dbtransporter->join("andalas_customer_company", "customer_company_id = job_customer_company", "left");
		$qr = $this->dbtransporter->get("andalas_job");
				
		$row = $qr->row();
		$params['row'] = $row;
				
		$callback['html'] = $this->load->view("job/set_delivered", $params, true);
		$callback['error'] = false;	
				
		echo json_encode($callback);
	}
	
	function save_delivered()
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
		
		$job_id = isset($_POST['job_id']) ? $_POST['job_id'] : 0;
		$date = isset($_POST['date']) ? $_POST['date'] : "";
		$time = isset($_POST['time']) ? $_POST['time'] : "";
		$deliv_datetime = date("Y-m-d H:i:s", strtotime($date . " " . $time));
		
		//Update Status Delivered (value:2) and datetime
		$status["job_status"] = 2;
		$status["job_deliv_date"] = $date;
		$status["job_deliv_time"] = $time;
		$status["job_deliv_datetime"] = $deliv_datetime;
		
		$this->dbtransporter->where("job_id", $job_id);
		$this->dbtransporter->update("andalas_job", $status);
		$this->dbtransporter->cache_delete_all();
			
		$callback['error'] = false;
		$callback['message'] = "Update Status Successfully Submitted";
		echo json_encode($callback);
			
		return;
		
	}
	
	function detail_job() {
		
		if ($this->sess->user_type != 1){
			if (!$this->sess->user_company){
			redirect(base_url());
			}
		}
		
		$user_company = $this->sess->user_company;
		$type_admin = $this->sess->user_type;
		$user_group = $this->sess->user_group;
		
		$id = $this->input->post("id");
		$this->load->helper(array('form'));

		$this->dbtransporter = $this->load->database("transporter", true);
		$this->dbtransporter->where("job_id", $id);
		$this->dbtransporter->join("driver", "driver_id = job_driver", "left");
		$this->dbtransporter->join("mobil", "mobil_id = job_mobil_id", "left");
		$this->dbtransporter->join("andalas_customer_company", "customer_company_id = job_customer_company", "left");
		$q = $this->dbtransporter->get("andalas_job");
		$row = $q->row();

		$this->dbtransporter->close();
		
		$params['row'] = $row;
		$params["title"] = "Detail Job File";		
		$params["job_id"] = $id;
		$html = $this->load->view("job/job_detail", $params, true);
		$callback['error'] = false;
		$callback['html'] = $html;		
		echo json_encode($callback);
		
	}
		 	  
}
